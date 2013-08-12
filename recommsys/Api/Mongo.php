<?php
//lianghonghao@baixing.com
namespace Searchable;
class Mongo extends \MongoData implements \SeachInterface{
	public function searchFields() {
		return $this->attributes();
	}

	public function save($flag = false, $index = true) {
		$id = parent::save($flag);
		if ($index) {
			\Queue::create('ExtendPostQueue')->push(array($id, get_called_class()));
		}
	}

	public function delete() {
		$result = parent::delete();
		$searcher = new \ExtendSearcher(null, array(), get_called_class());
		$searcher->delete($this->data[$this->key]);
		return $result;
	}

	public static function searcher($query = null, $options = array()) {
		return new \ExtendSearcher($query, $options, get_called_class());
	}
}

?>
