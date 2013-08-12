<?php
//zhouxiang@baixing.com
class VoterLog extends Searchable\Mongo {
	public function setExts($exts) {
		if (is_array($exts))
			$this->data += $exts;
	}
	
	public function save() {
		return parent::save(self::SAFE_LOW, $this->actionType != 'popup');
	}
	
	public function attributes() {
		$arr = parent::attributes();
		if (isset($arr['trackId'])) $arr['userTime'] = intval(substr($arr['trackId'], 0, 10));
		return $arr;
	}
	
	public function loads(array $ids) {
		$arr = array();
		foreach ($ids as $id) {
			if ($id instanceof MongoId) {
				$arr []= $id;
			} else {
				$arr []= new MongoId($id);
			}
		}
		return parent::loads($arr);
	}
}
?>
