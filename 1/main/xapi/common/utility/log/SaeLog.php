<?php

class SaeLog {
	public static function writelog_debug ($msg) {
		sae_set_display_errors(false);//关闭信息输出
		if (is_array($msg)) {
			$msg = implode(",", $msg);
		}
		sae_debug("[eduApp]".$msg);//记录日志
		sae_set_display_errors(true);//记录日志后再打开信息输出，否则会阻止正常的错误信息的显示
	}
}
