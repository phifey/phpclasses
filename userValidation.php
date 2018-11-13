<?php 
function strLength(string $str, int $minlength, int $maxlength)
{
	switch(true)
	{
		case(strlen($str) >= $minlength && strlen($str) <= $maxlength):
			return true;
			break;
		case(strlen($str) <= $minlength || strlen($str) >= $maxlength):
			return false;
			break;
	}
}
?>