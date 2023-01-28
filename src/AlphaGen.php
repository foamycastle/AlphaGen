<?php

namespace FoamyCastle\AlphaGen;
use FoamyCastle\AlphaGen\Enum\AlphaType;

class AlphaGen {
	private const DEFAULT_LENGTH=32;
	private const ALPHA_TABLE="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	private const NUMERIC_TABLE="0123456789";
	private const EXTENDED_TABLE="_.-";
	private int $length;
	private AlphaType $type;
	function __construct(AlphaType $type,int $length){
		$this->setType($type);
		$this->setLength($length);
	}
	function generate(bool $strong=false):string{
		$table=$this->getTable();
		$tableLength=strlen($table);
		$remainingChars=$totalLength=$this->getLength();
		$recentRandoms=[];$outputString="";
		do{
			/*
			 * If strong is enabled, maintain a queue (FIFO) of recently chosen numbers.  The maximum
			 * length of this array will be half of the desired output length. Continue to choose random numbers
			 * until the chosen number is not in the queue.
			 */
			if($strong){
				do{
					//choose random number
					$random=mt_rand(0,$tableLength-1);
					//continue to do so until the random number is not in the recent queue
				}while(in_array($random,$recentRandoms));
				//add to the queue.
				$recentRandoms[]=$random;
				//prune the queue if it becomes to large
				if(count($recentRandoms)>intval($totalLength/2)) array_shift($recentRandoms);
			}else{
				//$strong is disabled
				$random=mt_rand(0,$tableLength-1);
			}
			$outputString.=$table[$random];
			$remainingChars--;
		}while($remainingChars>0);
		return $outputString;
	}
	private function getTable():string{
		return self::ALPHA_TABLE.
			($this->type==AlphaType::ALPHA_NUMERIC ? self::NUMERIC_TABLE:"").
			($this->type==AlphaType::ALPHA_EXTENDED ? self::EXTENDED_TABLE:"");
	}
	public function setType(AlphaType $type):void{
		$this->type=$type;
	}
	public function setLength(?int $length=null):void{
		$this->length = $length ?? self::DEFAULT_LENGTH;
	}
	public function getLength():int{
		return $this->length;
	}
}