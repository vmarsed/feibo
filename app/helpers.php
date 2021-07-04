<?php

function get_db_config()
{
	// 不理解,getenv 读的不是 php.ini 吗,哪来的 IS_IN_HEROKU
	// Laravel .env 文件中的变量就是
	// 通过 putenv 函数注入到环境变量中来的
	// 当需要使用时就通过 getenv 取出来。
	// heroku 也是注入了吧,只能这么理解了

	if(getenv('IS_IN_HEROKU'))
	{
		$url = parse_url(getenv("DATABASE_URL"));

		return $db_config = [
			'connection'=>'pgsql',
			'host'=>$url["host"],
			'database'=>substr($url["path"],1),
			'username'=>$url["user"],
			'password'=>$url["pass"],
		];
	}else{
		// env 函数,第2个参数是默认值的意思
		return $db_config=[
			'connection'=>env('DB_CONNECTION','mysql'),
			'host'=>env('DB_HOST','localhost'),
			'database'=>env('DB_DATABASE','forge'),
			'username'=>env('DB_USERNAME','forge'),
			'password'=>env('DB_PASSWORD',''),
		];
	}
}