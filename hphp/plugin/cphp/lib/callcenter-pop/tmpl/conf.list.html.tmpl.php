<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css">
/*  */
.r0_b1,.r0_b2,.r0_b3,.r0_b4,.r0_b1b,.r0_b2b,.r0_b3b,.r0_b4b,.r0_b{display:block;overflow:hidden;}
.r0_b1,.r0_b2,.r0_b3,.r0_b1b,.r0_b2b,.r0_b3b{height:1px;}
.r0_b2,.r0_b3,.r0_b4,.r0_b2b,.r0_b3b,.r0_b4b,.r0_b{border-left:1px solid #046BB7;border-right:1px solid #046BB7;}
.r0_b1,.r0_b1b{margin:0 5px;background:#046BB7;}
.r0_b2,.r0_b2b{margin:0 3px;border-width:2px;}
.r0_b3,.r0_b3b{margin:0 2px;}
.r0_b4,.r0_b4b{height:2px;margin:0 1px;}
.r0_bg1{background:#D9D9D9;}
.r0_bh{height:100%;padding:5px;}

.r0_bh0{padding:5px;}
.r0_bh1{height:150px;padding:2px;}
.r0_bh2{height:200px;padding:3px;}
.r0_bh3{height:300px;padding:4px;}

.fnlist{border:1px solid #046BB7;width:200px;margin:2px;}
</style>
</head>
<body>
<div style="width:100%;">

<div style="margin:2px;width:100%;float:left;">
	<div class="r0_b1"></div>
	<div class="r0_b2 r0_bg1"></div>
	<div class="r0_b3 r0_bg1"></div>
	<div class="r0_b4 r0_bg1"></div>
	<div class="r0_b r0_bg1 r0_bh0">
<div style="text-align:center;">弹屏配置文件列表</div>
<?php foreach ( $tmpl['list'] as $k => $v ) { ?>
<div class="fnlist">
	<div style="width:120px;float:left;"><?=$k?></div><a href="?act=confedit&fn=<?=$k?>" target="body-edit">编辑</a>&nbsp;<a href="?act=confdel&fn=<?=$k?>" target="body-edit" onclick="return confirm('请确认删除配置文件:<?=$k?>');">删除</a>
</div>
<?php } ?>
<div style="border:2px solid #046BB7;margin:2px;width:120px;"><a href="?act=confadd" target="body-edit">新建配置文件</a></div>
	</div>
	<div class="r0_b4b r0_bg1"></div>
	<div class="r0_b3b r0_bg1"></div>
	<div class="r0_b2b r0_bg1"></div>
	<div class="r0_b1b"></div>
</div>

</div>
</body>
</html>
