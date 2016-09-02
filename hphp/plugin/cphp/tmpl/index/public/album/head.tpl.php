<div style="height:55px">
	<span style="float:left; text-align:left">
		<a href="/"><img src="/album/data/logo.gif" align="absbottom"></a>
		<span>
  			 哈囉！<!-- <a href="/mb/login">登入</a> --> {$login}
					</span> 
	</span>
	<span style="float:right;text-align:right">

		<a href="<?=url("?")?>">相簿首頁</a>&nbsp;|&nbsp;
		<a href="<?=url("?module=user&action=login")?>">會員中心</a>
		(<a href="<?=url("?module=user&action=register")?>">注册</a>)&nbsp;|&nbsp;
		<a href="#">尋求幫助</a>&nbsp;|&nbsp;
		<a href="http://www.addwe.com.tw/">AddWe</a>
	</span>

</div>
<div> 广告条 </div>
<div>
	<span style="float:left;text-align:right">
		<a href="<?=url('?')?>">相簿首頁</a>&nbsp;|&nbsp;
		<a href="<?=url('?module=user')?>">我的相簿</a>&nbsp;|&nbsp;
		<a href="<?=url('?action=sort&sid=top')?>">人气相簿</a>&nbsp;|&nbsp;
		<a href="<?=url('?action=sort&sid=hot')?>">热门相簿</a>&nbsp;|&nbsp;
		<a href="<?=url('?action=sort&sid=new')?>">网友投稿</a>
	</span><br>
	<span>
		相簿分类:{$sort}
	</span>
</div>