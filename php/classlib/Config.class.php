<?php

/* 
 
游戏服务器目录及版本目录需要的管理脚本 
 
1. 版本目录需要有更新版本的脚本 
    功能：
    	这个脚本负责把版本目录的内容更新到验收服、测试服或其他服务器(包括客户端，服务器和资源)
    原型：
    	update{$tar}.sh $ver $game 如：updatever.sh xl_0_110 xl
    说明：
		$tar 代表目录服务器 如：ver(验收服)，test(测试服) 
		$ver 代表版本号，如：xl_0_110, xj_0_310等
		$game游戏名[可选], 如：xl, mx, xj 等。如果此脚本只是共一个游戏用则脚本中可以忽略此参数
2. 版本目录需要有更新svn的脚本
	功能：
		这个脚本负责把上传到版本目录中的文件更新到svn中
	原型:
		svncommit.sh $path $comment
	说明：
		$path 代表 当前更新版本的目录。例如：/troodon/webgame/xg_bk/server/xg_201209/xg_0_0190 这个是仙迹的 190版本文件上传目录
		$comment 表示本次更新svn的注释，即更新内容说明
3. 每个游戏服务器目录中需要有更新和重启本服务器的脚本 
    功能：
    	关闭或重启本目录的游戏服务器进程（每个游戏服务器目录必须有这两个shell）
    原型：
		stop.sh
    	restart.sh
    说明：
    	stop.sh 负责关闭当前目录的游戏进程
		restart.sh 负责关闭当前目录的游戏进程（如游戏服务器进程存在）并重新其他游戏服务器进程
*/





//  上传 csv 和 map 相关内容用到的常量
define("SVRPATH", "server/");                      // 版本服务器版本目录（版本目录的子目录）
define("SWFPATH", "swf/");                         // 版本客户端版本目录（版本目录的子目录）
define("RESPATH", "res/");                         // 版本资源目录（版本目录的子目录）

define("CSVSUFFIX", "csv,zip");                    // csv文件后缀名
define("MAPSUFFIX", "sj,jpg,zip");                 // 地图文件后缀名
define("CSVPATH", "svr/g/data/");                  // 游戏服务器存放csv文件的目录（相对目录）
define("MAPPATH", "map/");                         // 游戏服务器存盘地图信息文件的目录(相对目录)

define("UPLOAD", "upload/");                       // php 上传文件目录



/**
 * 不同的游戏配置都在此类中
 * 
 * @author troodon (2012/7/13)
 */
class Config {
	// 仙路游戏配置
	public static $xl = array(
		// 游戏服的不同环境的ip，bk是版本更新目录。（上传文件、更新版本、查询服务器状态等情况下使用）【基础配置】
		"svrip" => array(
			"def" => "root@192.168.11.53",  // 默认ip(如果指定的服务器没有配置则使用默认ip)
			"test" => "root@192.168.11.46",  // 指定某个服务器的ip，服务器类型包括 dev,ver,bk,test
			),

		// 游戏服的不同环境的根目录，bk是版本更新目录。（上传文件、更新版本、查询服务器状态等情况下使用）【基础配置】
		"svrroot" => array(
			"dev"  => "/home/mx_dev/",
			"bk"   => "/home/xl_bk/",
			"ver"  => "/home/xl_s/",
			"test" => "/troodon/webgame/xl/",
			), 

		//每个游戏的不同环境下的不同功能服子目录数组（每个数组的第一项必须是正常服：上传、更新时使用）（服务器管理模块）
		"svrfolder"   => array(
			"dev"  => array("gs","dts","dld","kfz","wlzb","bhtg"),  // 不同共功能服的子目录
			"ver"  => array("gameserver","dts","dld","kfz","wlzb","bhjs"),
			"test" => array("xl0001g1","xl0002g1","xl0003g1","xld001g1","xll001g1","xlk001g1","xlw001g1","xlb001g1"),
			"dev_name"  => array("正常服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"), // 不同功能服子目录的中文名字
			"ver_name"  => array("正常服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"),
			"test_name" => array("一服","二服","三服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"),
			), 

		// 资源服务器ip
		"resip" => array(
			"def"  => "root@192.168.10.34", //默认ip 
			//"test" => "root@192.168.11.46",  // 指定某个服的ip
			),

		// 资源所在目录(根目录)
		"resroot" => array(
			"dev"  => "/home/htdocs/xl/dev/",
			"ver"  => "/home/htdocs/xl/ver/",
			), 

		// 资源所在文件夹，不同类型的文件在不同的文件夹
		"resfolder" => array(
			"dev"  => array("flash" => "game"),
			"ver"  => array("flash" => "game"),
			), 

		// 产品的svn安装路径，只有版本里用所以不用分服务器类型。（上传文件，更新版本时用）
		"svrsvn" => "/usr/local/svn/bin/svn", 

		// 游戏版本头（上传文件，更新版本时用）
		"svrver" => "xl_",
	);

	// 梦仙游戏配置
	public static $mx = array(
		// 游戏服的不同环境的ip，bk是版本更新目录。（上传文件、更新版本、查询服务器状态等情况下使用）【基础配置】
		"svrip" => array(
			"def" => "root@192.168.11.53",  // 默认ip(如果指定的服务器没有配置则使用默认ip)
			"test" => "root@192.168.11.46",  // 指定某个服务器的ip，服务器类型包括 dev,ver,bk,test
			),

		// 游戏服的不同环境的根目录，bk是版本更新目录。（上传文件、更新版本、查询服务器状态等情况下使用）【基础配置】
		"svrroot" => array(
			"dev"  => "/home/mx_dev/",
			"bk"   => "/home/xl_bk/",
			"ver"  => "/home/mx_s/",
			"test" => "/troodon/webgame/mx/",
			), 

		//每个游戏的不同环境下的不同功能服子目录数组（每个数组的第一项必须是正常服：上传、更新时使用）（服务器管理模块）
		"svrfolder"   => array(
			"dev"  => array("gs","dts","dld","kfz","wlzb","bhjs"),  // 不同共功能服的子目录
			"ver"  => array("gameserver","dts","dld","kfz","wlzb","bhjs"),
			"test" => array("mx0001g1","mx0002g1","mx0003g1","mxd001g1","mxl001g1","mxk001g1","mxw001g1","mxb001g1"),
			"dev_name"  => array("正常服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"), // 不同功能服子目录的中文名字
			"ver_name"  => array("正常服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"),
			"test_name" => array("一服","二服","三服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"),
			), 

		// 资源服务器ip
		"resip" => array(
			"def"  => "root@192.168.10.34", //默认ip 
			//"test" => "root@192.168.11.46",  // 指定某个服的ip
			),

		// 资源所在目录(根目录)
		"resroot" => array(
			"dev"  => "/home/htdocs/mx/dev/",
			"ver"  => "/home/htdocs/mx/ver/",
			), 

		// 资源所在文件夹，不同类型的文件在不同的文件夹
		"resfolder" => array(
			"dev"  => array("flash" => "game"),
			"ver"  => array("flash" => "game"),
			), 

		// 产品的svn安装路径，只有版本里用所以不用分服务器类型。（上传文件，更新版本时用）
		"svrsvn" => "/usr/local/svn/bin/svn", 

		// 游戏版本头（上传文件，更新版本时用）
		"svrver" => "xl_",
	);

	// 仙迹游戏配置
	public static $xg = array(
		// 游戏服的不同环境的ip，bk是版本更新目录。（上传文件、更新版本、查询服务器状态等情况下使用）【基础配置】
		"svrip" => array(
			"def" => "root@192.168.11.53",  // 默认ip(如果指定的服务器没有配置则使用默认ip)
			"test" => "root@192.168.11.46",  // 指定某个服务器的ip，服务器类型包括 dev,ver,bk,test
			),

		// 游戏服的不同环境的根目录，bk是版本更新目录。（上传文件、更新版本、查询服务器状态等情况下使用）【基础配置】
		"svrroot" => array(
			"dev"  => "/home/xj_dev/",
			"bk"   => "/troodon/webgame/xg_bk/",
			"ver"  => "/troodon/webgame/xg_s/",
			"test" => "/troodon/webgame/xg/",
			), 

		//每个游戏的不同环境下的不同功能服子目录数组（每个数组的第一项必须是正常服：上传、更新时使用）（服务器管理模块）
		"svrfolder"   => array(
			"dev"  => array("gs","dts","dld","kfz","wlzb","bhjs"),  // 不同共功能服的子目录
			"ver"  => array("gameserver","dts","dld","kfz","wlzb","bhjs"),
			"test" => array("xg0001g1","xg0002g1","xg0003g1","xgd001g1","xgl001g1","xgk001g1","xgw001g1","xgb001g1"),
			"dev_name"  => array("正常服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"), // 不同功能服子目录的中文名字
			"ver_name"  => array("正常服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"),
			"test_name" => array("一服","二服","三服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"),
			), 

		// 资源服务器ip
		"resip" => array(
			"def"  => "root@192.168.10.34", //默认ip 
			//"test" => "root@192.168.11.46",  // 指定某个服的ip
			),

		// 资源所在目录(根目录)
		"resroot" => array(
			"dev"  => "/home/htdocs/js/dev/", // 开发服仙迹和九世用的一个
			"ver"  => "/home/htdocs/xg/ver/",
			), 

		// 资源所在文件夹，不同类型的文件在不同的文件夹
		"resfolder" => array(
			"dev"  => array("flash" => "game"),
			"ver"  => array("flash" => "game"),
			), 

		// 产品的svn安装路径，只有版本里用所以不用分服务器类型。（上传文件，更新版本时用）
		"svrsvn" => "/usr/local/svn/bin/svn", 

		// 游戏版本头（上传文件，更新版本时用）
		"svrver" => "xg_",
	);

	// 九世游戏配置
	public static $tg = array(
		// 游戏服的不同环境的ip，bk是版本更新目录。（上传文件、更新版本、查询服务器状态等情况下使用）【基础配置】
		"svrip" => array(
			"def" => "root@192.168.11.53",  // 默认ip(如果指定的服务器没有配置则使用默认ip)
			"test" => "root@192.168.11.46",  // 指定某个服务器的ip，服务器类型包括 dev,ver,bk,test
			),

		// 游戏服的不同环境的根目录，bk是版本更新目录。（上传文件、更新版本、查询服务器状态等情况下使用）【基础配置】
		"svrroot" => array(
			"dev"  => "/home/xj_dev/",
			"bk"   => "/troodon/webgame/xg_bk/",
			"ver"  => "/troodon/webgame/tg_s/",
			"test" => "/troodon/webgame/tg/",
			), 

		//每个游戏的不同环境下的不同功能服子目录数组（每个数组的第一项必须是正常服：上传、更新时使用）（服务器管理模块）
		"svrfolder"   => array(
			"dev"  => array("gs","dts","dld","kfz","wlzb","bhjs"),  // 不同共功能服的子目录
			"ver"  => array("gameserver","dts","dld","kfz","wlzb","bhjs"),
			"test" => array("tg0001g1","tg0002g1","tg0003g1","tgd001g1","tgl001g1","tgk001g1","tgw001g1","tgb001g1"),
			"dev_name"  => array("正常服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"), // 不同功能服子目录的中文名字
			"ver_name"  => array("正常服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"),
			"test_name" => array("一服","二服","三服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"),
			), 

		// 资源服务器ip
		"resip" => array(
			"def"  => "root@192.168.10.34", //默认ip 
			//"test" => "root@192.168.11.46",  // 指定某个服的ip
			),

		// 资源所在目录(根目录)
		"resroot" => array(
			"dev"  => "/home/htdocs/tg/dev/",
			"ver"  => "/home/htdocs/tg/ver/",
			), 

		// 资源所在文件夹，不同类型的文件在不同的文件夹
		"resfolder" => array(
			"dev"  => array("flash" => "game"),
			"ver"  => array("flash" => "game"),
			), 

		// 产品的svn安装路径，只有版本里用所以不用分服务器类型。（上传文件，更新版本时用）
		"svrsvn" => "/usr/local/svn/bin/svn", 

		// 游戏版本头（上传文件，更新版本时用）
		"svrver" => "xg_",
	);

	// 仙迹-越南版游戏配置
	public static $yn = array(
		// 游戏服的不同环境的ip，bk是版本更新目录。（上传文件、更新版本、查询服务器状态等情况下使用）【基础配置】
		"svrip" => array(
			"def" => "root@192.168.11.53",  // 默认ip(如果指定的服务器没有配置则使用默认ip)
			"test" => "root@192.168.11.46",  // 指定某个服务器的ip，服务器类型包括 dev,ver,bk,test
			),

		// 游戏服的不同环境的根目录，bk是版本更新目录。（上传文件、更新版本、查询服务器状态等情况下使用）【基础配置】
		"svrroot" => array(
			"dev"  => "/home/xj_dev/",
			"bk"   => "/troodon/webgame/xg_bk/",
			"ver"  => "/troodon/webgame/yn_s/",
			"test" => "/troodon/webgame/yn/",
			), 

		//每个游戏的不同环境下的不同功能服子目录数组（每个数组的第一项必须是正常服：上传、更新时使用）（服务器管理模块）
		"svrfolder"   => array(
			"dev"  => array("gs","dts","dld","kfz","wlzb","bhjs"),  // 不同共功能服的子目录
			"ver"  => array("gameserver","dts","dld","kfz","wlzb","bhjs"),
			"test" => array("yn0001g1","yn0002g1","yn0003g1","ynd001g1","ynl001g1","ynk001g1","ynw001g1","ynb001g1"),
			"dev_name"  => array("正常服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"), // 不同功能服子目录的中文名字
			"ver_name"  => array("正常服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"),
			"test_name" => array("一服","二服","三服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"),
			), 

		// 资源服务器ip
		"resip" => array(
			"def"  => "root@192.168.10.34", //默认ip 
			//"test" => "root@192.168.11.46",  // 指定某个服的ip
			),

		// 资源所在目录(根目录)
		"resroot" => array(
			"dev"  => "/home/htdocs/yn/dev/",
			"ver"  => "/home/htdocs/yn/ver/",
			), 

		// 资源所在文件夹，不同类型的文件在不同的文件夹
		"resfolder" => array(
			"dev"  => array("flash" => "game"),
			"ver"  => array("flash" => "game"),
			), 

		// 产品的svn安装路径，只有版本里用所以不用分服务器类型。（上传文件，更新版本时用）
		"svrsvn" => "/usr/local/svn/bin/svn", 

		// 游戏版本头（上传文件，更新版本时用）
		"svrver" => "xg_",
	);
	// 千年之恋游戏配置
	public static $qn = array(
		// 游戏服的不同环境的ip，bk是版本更新目录。（上传文件、更新版本、查询服务器状态等情况下使用）【基础配置】
		"svrip" => array(
			"def" => "root@192.168.11.53",  // 默认ip(如果指定的服务器没有配置则使用默认ip)
			"test" => "root@192.168.11.46",  // 指定某个服务器的ip，服务器类型包括 dev,ver,bk,test
			),

		// 游戏服的不同环境的根目录，bk是版本更新目录。（上传文件、更新版本、查询服务器状态等情况下使用）【基础配置】
		"svrroot" => array(
			"dev"  => "/home/xj_dev/",
			"bk"   => "/troodon/webgame/xg_bk/",
			"ver"  => "/troodon/webgame/qn_s/",
			"test" => "/troodon/webgame/qn/",
			), 

		//每个游戏的不同环境下的不同功能服子目录数组（每个数组的第一项必须是正常服：上传、更新时使用）（服务器管理模块）
		"svrfolder"   => array(
			"dev"  => array("gs","dts","dld","kfz","wlzb","bhjs"),  // 不同共功能服的子目录
			"ver"  => array("gameserver","dts","dld","kfz","wlzb","bhjs"),
			"test" => array("qn0001g1","qn0002g1","qn0003g1","qnd001g1","qnl001g1","qnk001g1","qnw001g1","qnb001g1"),
			"dev_name"  => array("正常服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"), // 不同功能服子目录的中文名字
			"ver_name"  => array("正常服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"),
			"test_name" => array("一服","二服","三服","大逃杀","大乱斗","跨服战","武林争霸","帮会竞赛"),
			), 

		// 资源服务器ip
		"resip" => array(
			"def"  => "root@192.168.10.34", //默认ip 
			//"test" => "root@192.168.11.46",  // 指定某个服的ip
			),

		// 资源所在目录(根目录)
		"resroot" => array(
			"dev"  => "/home/htdocs/qn/dev/",
			"ver"  => "/home/htdocs/qn/ver/",
			), 

		// 资源所在文件夹，不同类型的文件在不同的文件夹
		"resfolder" => array(
			"dev"  => array("flash" => "game"),
			"ver"  => array("flash" => "game"),
			), 

		// 产品的svn安装路径，只有版本里用所以不用分服务器类型。（上传文件，更新版本时用）
		"svrsvn" => "/usr/local/svn/bin/svn", 

		// 游戏版本头（上传文件，更新版本时用）
		"svrver" => "xg_",
	);
}


?>
