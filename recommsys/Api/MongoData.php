<?php
//lianghonghao@baixing.com

class MongoData {
	protected $data = array();
	protected $connection;
	protected $key = '_id';
	public static $counter = 0;	
	public $className = null;	//@todo 改名 =》 table name

	const SAFE_NORMAL = true;
	const SAFE_HIGH = 2;
	const SAFE_LOW = false;

	/**
	 *  @param	mixed $id 形如 'yubing' or 'yubing.name'(用点分割)
	 *  @return mixed 返回$this or 'yubing.name'字段的值
	 */
	public function load($id) {
		if (!$id) return null;
		self::$counter++; //查询计数
		
		$pos = strpos($id, '.');
		try {
			if ($pos) {
				list($id, $subId) = explode('.', $id, 2);
				$data = $this->connection->findOne(array($this->key => $id), array($subId => 1));
				return isset($data[$subId]) ? $data[$subId] : null;
			} else {
				$this->data = $this->connection->findOne(array($this->key => $id));
				return $this;
			}
		} catch (MongoException $exc) {
			if ($exc->getCode() != 12)	throw $exc;
		}
	}

	public function attributes() {
		$arr = $this->data;
		if (isset($arr['_id'])) {
			$arr['id'] = (string)$arr['_id'];
			unset($arr['_id']);
		}
		return $arr ?: array();
	}
	/*
	 * @return MongoDB
	 */
	protected function db() {
		return $this->connection->db;
	}
	
	/*
	 * @return array
	 */
	public function loads(array $ids) {
		if (empty($ids)) return array();
		$objects = $this->find(array(
			$this->key => array(
				'$in' => $ids,
			)
		));
		$return = array_fill_keys($ids, FALSE);
		foreach ($objects as $each_id => $each_object) {
			$return[$each_id] = $each_object;
		}
		return array_filter($return, function($obj){return $obj !== FALSE;});
	}
	
	/*
	 * @return bool
	 */
	public function update($criteria, $newobj, $options=array()) {
		return $this->connection->update($criteria, $newobj, $options);
	}

	public function count($data = null) {
		if ($data instanceof Query) {
			$data = $data->mongoQuery();
		}
		return $this->connection->count($data ?: $this->data);
	}

	public function delete() {
		if (isset($this->data[$this->key])) {
			return $this->connection->remove(array($this->key => $this->data[$this->key]));
		}
	}
	
	protected function filterhtml(&$value) {
		$value = htmlspecialchars($value, ENT_NOQUOTES, 'ISO-8859-1', false);
	}
	
	public function htmlspecialchars() {
		array_walk_recursive($this->data, array($this, 'filterhtml'));
	}
	
	public function save($safe = self::SAFE_NORMAL) {
		if (ENVIRONMENT != 'production') $safe = (bool) $safe;
		$this->data = array_filter($this->data, function($v){return $v !== null;});
		if (!isset($this->data[$this->key]) && $this->key !== '_id') {
			throw new Exception();
		}
		try {
			if (!isset($this->data['_id'])) {
				$this->connection->insert($this->data, array('safe' => $safe));
			} else {
				$this->connection->update(array($this->key => $this->data[$this->key]), $this->data, array("upsert" => true, "safe" => $safe));
			}
		} catch(Exception $e) {
			Logger::error('MongoDB写错误', $e->getMessage() . "\n". var_export($this->data, true), 'lianghonghao@baixing.com');
			return false;
		}
		return $this->data[$this->key];
	}

	public function setAttr($query, $field, $value) {
		return $this->update($query->mongoQuery(), array('$set' => array($field => $value)));
	}

	public function push($name, $value) {
		return $this->update(array($this->key => $this->data[$this->key]), array('$push' => array($name => $value)));
	}
	
	public function addToSet($name, $value) {
		return $this->update(array($this->key => $this->data[$this->key]), array('$addToSet' => array($name => $value)));
	}
	
	public function find($data = null, $option = array()) {
		self::$counter++; //查询计数
		if(is_a($data, 'Query')) {
			$data = $data->mongoQuery();
		} else {
			$data = $data ?: $this->data;
		}
		$datas = array();
		$cmd_arr = array('sort' => 1, 'limit' => 1, 'skip' => 1);
		try {
			$cur = $this->connection->find($data, array_diff_key($option, $cmd_arr))->timeout(1000);	//屏蔽了sort limit等几个操作符
			foreach($cmd_arr as $cmd => $val) {
				if(isset($option[$cmd])) $cur->$cmd($option[$cmd]);
			}
			if (!empty($cur)) {
				$className = get_called_class();
				foreach ($cur as $eachDoc) {
					$obj = new $className();
					$obj->data = $eachDoc;
					$datas[strval($eachDoc[$this->key])] = $obj;
				}
			}
		} catch (MongoException $exc) {
			if ($exc->getCode() != 12) throw $exc;
		}
		return $datas;
	}

	public function groupBy($keys, $initial, $reduce, $condition=null) {
		$condition = $condition ? :$this->data;
		$ret       = $this->connection->group($keys, $initial, $reduce, $condition);
		return $ret['retval'];
	}
	
	public function __construct($id = null) {
		if (!isset($this->key)) $this->key = '_id';
		if (!isset($this->connection)) {
			$this->connection = MongoDataConnection::getInstance($this->className ?: get_called_class ());
		}
		if (isset($id)) {
			$this->load($id);
			if (!$this->data) $this->data[$this->key] = $id;	///没load到的设置一下，当做初始化变量
		}
	}
	
	public function __get($name) {
		if ($name == 'id') $name='_id';
		return isset($this->data[$name]) ? $this->data[$name] : null;
	}
	
	public function __set($name, $value) {
		$this->data[$name] = $value;
	}
	
}

class MongoDataConnection {
	private static $connections = array();
	
	public static function getInstance($dataType) {
		if (!isset(self::$connections[$dataType])) {
			try {
				$connection = new Mongo(Config::item('env.mongodb.server'), Config::item('env.mongodb.option'));
			} catch (MongoConnectionException $e) {
				$connection = new BlackHole();
				return self::$connections[$dataType] = $connection;
			}
			self::$connections[$dataType] = $connection->selectDB('chaoge')->selectCollection($dataType);
			self::$connections[$dataType]->setSlaveOkay();
		}
		return self::$connections[$dataType];
	}
}
?>