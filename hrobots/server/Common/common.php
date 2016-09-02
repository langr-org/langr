<?php
/**
 * UTF8 中文字符串截取
 * @param   string	$str		
 * @param   int		$length	 
 * @param   bool	$append	
 * @return  string
 */
function sub_str($str, $length = 0, $append = true)
{
	$str = trim($str);
	$strlength = strlen($str);

	if ($length == 0 || $length >= $strlength) {
		return $str;
	} else if ($length < 0) {
		$length = $strlength + $length;
		if ($length < 0) {
			$length = $strlength;
		}
	}

	if (function_exists('mb_substr')) {
		$newstr = mb_substr($str, 0, $length, 'utf-8');
	} else if (function_exists('iconv_substr')) {
		$newstr = iconv_substr($str, 0, $length, 'utf-8');
	} else {
		$newstr = substr($str, 0, $length);
	}

	if ($append && $str != $newstr) {
		$newstr .= '...';
	}

	return $newstr;
}

/**
 * 处理表单提交的字符过滤
 *
 * @param   string	$string 需要处理的字符串		
 * @param   int		$trim   需要删除空格	 
 * @return  string
 */
function tpaddslashes($string = '' , $trim = true ,$js = false) {
	$arr = array('<' => '＜', '>' => '＞'); //防止script注入
	if(!get_magic_quotes_gpc()) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				
				if ($js) {
					$val		  = strtr($val, $arr);
				}
				$string[$key] = tpaddslashes($val, $trim);
			}
		} else {
			if ($trim) {
				$string = trim($string);
			}
			
			if ($js) {
				$string		= strtr($string, $arr);
			}
			$string		= addslashes($string);
		}
	}
	return $string;
}

/**
 * 根据分类ID获取分类名称
 * 
 * @param integer $cat_id
 * @return string
 */
function get_cat_name_by_id($cat_id = 0) {
	$cat_id = intval($cat_id);
	if ($cat_id > 0) {
		$Cat = D('category');
		$cat_info = $Cat->cache(true)->field('cat_name')->where("cat_id='$cat_id'")->select();
		return $cat_info[0]['cat_name'];
	}
}

/**
 * 通过价格区间，获取单价
 * @param  array $price      价格区间
 * @return float $unit_price 单价
 */
function get_item_unit_price($price = array()) {
	if (empty($price)) return '0.0000';
	else {
		return $price['0']['2'];
	}
}

/**
 * 获取产品详细信息
 * @param integer or array $goods_id 商品ID
 * @param integer    $rec_type       商品类型，0普通商品，1团购商品
 * @return array
 */
function get_goods_info_by_id($goods_id = '', $rec_type = 0) {
	/* id为空返回为空 */
	if(!$goods_id) return array();

	if ($rec_type == 0) {
		$BigData    = &init_BigData();
		$info	= $BigData->BigDataGoodsInfo($goods_id,'goods_info','json');
		if ($info) {
			/* 批量查询供应商物料信息 */
			if (is_array($goods_id)) {
				foreach ($info as $k => $v) {
					//print_r($v);
					$item['goods_id']   = $v['ID'];
					$item['goods_desc'] = $v['ItemDesc'];
					$item['goods_name'] = $v['ModelCode'];
					$item['is_rohs']    = $v['Rohs'];
					$item['item_id']    = $v['ItemID'];
					$item['min_buynum'] = $v['MOQ'];
					$item['goods_number']=$v['Quantity']?$v['Quantity']:0;
					$item['cat_id']		= $v['Category3'];
					$item['shop_price'] = $v['Price'];
					$item['hqid']		= $v['HQID'];		//原商品ID
					$item['encap']		= $v['Encap'];		//封装
					$item['package']	= $v['Package'];	//包装
					$item['spq']		= $v['SPQ'];		//标准包装量
					$item['warehost']   = $v['Warehouse']; //仓库
					$item['is_split']   = $v['Split'];		//是否拆包
					$item['card_code']  = $v['CardCode'];  //供应商编码
					$item['goods_img']  = get_goods_thumb($v['ModelCode'],'big');
					$item['goods_thumb']= get_goods_thumb($v['ModelCode'],'small');
					/* 价格 */
					$item['price']      = price_detail($v['Items']);
					$item['shop_price'] = get_item_unit_price($item['price']);
					$item['shop_price_format'] = price_format($item['shop_price']);
					$item['url']        = build_uri('Product',array('gid'=>$v['ID']));
					$item['rec_type']   = $rec_type;
					if ($v['BrandID']) {
						$brand_info =  get_brand_info_by_brandid($v['BrandID']);
						$item['provider_name'] = $brand_info['brand_name'];
						$item['brand_id']		= $brand_info['brand_id'];
					}

					if ($item['price']) {
						$item['min_inquire_num']  = min_inquire_num($item['price']);  //最小询价数量
					}
					$goods[$v['ID']] = $item;
				}
				unset($info);
			} else {
				$goods['goods_id']   = $goods_id;
				$goods['rec_type']   = $rec_type;
				$goods['goods_desc'] = $info['0']['ItemDesc'];
				$goods['goods_name'] = $info['0']['ModelCode'];
				$goods['is_rohs']    = $info['0']['Rohs'];
				$goods['min_buynum'] = $info['0']['MOQ'];
				$goods['item_id']	 = $info['0']['ItemID'];
				$goods['goods_number']=$info['0']['Quantity']?$info['0']['Quantity']:0;
				$goods['cat_id']	 = $info['0']['Category3'];
				$goods['hqid']		 = $info['0']['HQID'];		//原商品ID
				$goods['encap']		 = $info['0']['Encap'];		//封装
				$goods['package']	 = $info['0']['Package'];	//包装
				$goods['spq']		 = $info['0']['SPQ'];		//标准包装量
				$goods['warehost']   = $info['0']['Warehouse']; //仓库
				$goods['is_split']   = $info['0']['Split'];		//是否拆包
				$goods['card_code']  = $info['0']['CardCode'];  //供应商编码
				$goods['goods_img']  = get_goods_thumb($info['0']['ModelCode'],'big');
				/* 价格 */
				$goods['price']      = price_detail($info['0']['Items']);
				$goods['url']        = build_uri('Product',array('gid'=>$goods_id));
				$goods['shop_price'] = get_item_unit_price($goods['price']);
				$goods['shop_price_format'] = price_format($goods['shop_price']);
				if ($info['0']['BrandID']) {
					$brand_info =  get_brand_info_by_brandid($info['0']['BrandID']);
					$goods['provider_name'] = $brand_info['brand_name'];
					$goods['brand_id']		= $brand_info['brand_id'];
				}

				if ($goods['price']) {
					$goods['min_inquire_num']  = min_inquire_num($goods['price']);  //最小询价数量
				}
				unset($info);
			}
		}
	} else {
		$goods = get_group_info_by_id($goods_id);		
	}
	
	return $goods;
}

/**
 * 获取产品简单的详细信息
 * 
 * @param integer or array $goods_id
 * @return array
 */
function get_small_goods_info_by_id($goods_id) {
	//$goods_id = intval($goods_id);
	if(!$goods_id) return array();
	
	//从缓存中取得数据
	//$res = S('goods_info_'.$goods_id);
	$BigData    = &init_BigData();
	$info	= $BigData->BigDataGoodsInfo($goods_id,'goods_info','json');
	
	if ($info) {
		/* 批量查询供应商物料信息 */
		if (is_array($goods_id)) {
			foreach ($info as $k => $v) {
				$item['goods_id']   = $v['ID'];
				$item['goods_name'] = $v['ModelCode'];
				$item['brand_id']   = $v['BrandID'];
				$item['item_id']    = $v['ItemID'];
				$item['goods_number']=$v['Quantity']?$v['Quantity']:0;
				$goods[$v['ID']]    = $item;
			}
			unset($info);
		} else {
			$goods['goods_id']   = $goods_id;
			$goods['goods_name'] = $info['0']['ModelCode'];
			$goods['brand_id']	 = $info['0']['BrandID'];
			$goods['item_id']    = $info['0']['ItemID'];
			$goods['goods_number']=$info['0']['Quantity']?$info['0']['Quantity']:0;
			unset($info);
		}
	}
	
	return $goods;
}

/**
 * 获取产品详细信息
 * 
 * @param integer or array $goods_id
 * @return array
 */
function get_item_infos($goods_id) {
	if(!$goods_id) return array();
	$BigData    = &init_BigData();
	$info	= $BigData->BigDataItemId($goods_id,'goods_item_id','json');
	
	if ($info) {
		/* 批量查询供应商物料信息 */
		if (is_array($goods_id)) {
			foreach ($info as $k => $v) {
				$goods[$v['ID']] = $v['ItemID'];
			}
			unset($info);
		} else {
			$goods[$v['ID']]	 = $info['0']['ItemID'];
		}
	}
	
	return $goods;
}

/**
 * 通过密码，产生加密后的密码以及密钥
 * @param  string  $pwd		   密码明文
 * @return array  $result      供应商物料参数
 */
function pwd_and_salt($pwd = '') {
	
	$pwd_salt	= substr(md5(rand(10000,99999)),0,8);
	$pwd		= md5($pwd_salt.(md5($pwd)));
	
	return array('pwd' => $pwd , 'pwd_salt' => $pwd_salt);
}

/**
 * 小数截取函数
 网站单价显示到小数点后第四位；购买时（购物车合计价格，订单应付价格，BOM计价），
 显示到小数点后两位。购买时，小数点后第三，第四位处理方法：为00，则舍弃；大于>00,
 则往前进位（范例：为1.1245时，购买时应付价格进位，为1.13；为1.1200时，购买时应付价格为1.12）
 大数据价格有小数点后第五、第六位，网站显示小数点后四位，对第五、第六位处理方法：为00，则舍弃；
 大于>00,则往前进位（范例：为1.124511时，购买时应付价格进位，为1.1446；为1.121300时，购买时应付价格为1.1213）
 * @param float $price 原始价格
 * @param string $sign 价格类型，two保留2位小数
 * @return float
 */
function number_format_temp($price = '' , $sign = '') {
	/* 格式化为6位小数 */
	if ($sign == 'two') {
		return number_format($price,2,".","");
	}
	if (empty($price)) {
		return number_format($price,4,".","");
	}
}

/**
 * 价格格式化（保留两位小数）
 * @param float $price
 * @param intval $money_type 价格类型1为人民币，2为美元
 * @param string $sign 结算 two保留2位，显示保留4位
 * @return float
 */
function price_format($price = 0 , $money_type = 0,$sign = '') {
	if ($price > 0) {
		if ($sign == 'two') {
			$price = number_format_temp($price,'two');
		} else {
			$price = number_format_temp($price);
		}
	} else {
		$price = 0;
	}
	
	if ($money_type) {
		return price_currency_format($money_type).$price;
	}
	return $price;
}

/**
 * 金额格式
 * @param int $money_type  金额类型 '':默认不带字符前缀，CNY：￥ USD:$
 * @return float 
 */
function price_pre($money_type = '') {
	if ($money_type == 'CNY') {
		return "￥";
	} elseif ($money_type == 'USD') {
		return "$";
	} else {
		return "";
	}
}

/**
 * 获取远程商品的信息
 *
 * @param string $url
 * @param integer $goods_id
 * @param integer $site
 * @return array
 */
function get_remote_goods_info ($url, $goods_id, $site = 0) {
	$goods_id = intval($goods_id);
	if ($goods_id > 10000000) { // 合作商商品
		$Goods = M('goods2');
	} else {
		$Goods = M('goods');
	}
	//$goods_info = $Goods->field('digikey_url,source_type,goods_type')->where("goods_id='$goods_id'")->find();
	$goods_info = get_goods_info_by_id($goods_id);
	$goods_type = intval($goods_info['goods_type']);
	$url = $goods_info['digikey_url'];
	$source_type = intval($goods_info['source_type']);
	if ($goods_type == 0) {
		//供应商商品信息
		$return_arr = get_merchants_goods_info($goods_id);
	} else {
		if ($site != $source_type) {
			$GoodsSourceUrl = M('goods_source_url');
			$url = $GoodsSourceUrl->where("goods_id='$goods_id' AND source_type='$site'")->getField('source_url');
		}
		//源类型（0：现货，1：DIGIKEY，2：MOUSER，3：FUTURE）
		if ($source_type != 0) {// 过滤非法请求
			$site = $source_type;
			$url = $goods_info['digikey_url'];
		}  
		if ($site == 2) {
			$return_arr = get_mouser_goods_info($url);
		} elseif ($site == 3) { 
			$return_arr = get_future_goods_info($url);
		} else {
			$url = 'http://www.digikey.cn' . $goods_info['digikey_url'];
			$return_arr = get_digikey_goods_info($url);
		}
	}

	if ($goods_id > 0) {
		//S('goods_' . $goods_id . '_' . $source_type, $return_arr);
		//S('goods_' . $goods_id, $return_arr);
		TCACHE('goods_price_number_'.$goods_id,$return_arr,C('CATE_ATTR_EXPIRE_TIME'));
	}
	return $return_arr;
}

/**
 * 获取合作商实时价格存库
 * @param integer $goods_id
 * @return array
 */
function get_merchants_goods_info($goods_id) {
	$goods_id = intval($goods_id);
	if ($goods_id) {
		$Goods = M('goods2');
		$GoodsPrice = M('goods_price');
		$now = time_local();
		$goods_number = $Goods->where("goods_id='$goods_id'")->getField('goods_number');
		$pricing = $GoodsPrice->field('purchases,unit_price')->where("goods_id='$goods_id'")->order('purchases')->select();
		$pricing_arr = array();
		if ($pricing) {
			foreach ($pricing as $key => $val) {
				$pricing_arr[$key][0] = $val['purchases'];
				$pricing_arr[$key][1] = cover_price($val['unit_price'], 0);
				$pricing_arr[$key][3] = cover_price($val['unit_price'], 3);
			}
		}
		
		return array('goods_number' => $goods_number, 'pricing' => $pricing_arr, 'time' => $now);
	}
}

/**
 * 获取DIGIKEY实时价格库存
 * @param string $url
 * @param integer $goods_id
 * @return array
 */
function get_digikey_goods_info ($url) {
	$pattern = '%http://www.digikey.cn/product-detail/zh/.+%';
	$return_arr = array();
	$now = time_local();
	if (preg_match($pattern, $url)) {
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 1);
		$html = curl_exec($ch);
		curl_close($ch);
		preg_match_all('%<table\sid=pricing.+?>(\s*)[^\<\/table>]</table>%s', $html, $out);
		$pricing_table = $out[0][0];  // 获取到价格表格
		preg_match_all('%<td.+?>([0-9\.,|^Call]+)<\/td>%s', $pricing_table, $out);
		$num_arr = $out[1];   // 表格提取数据
		$i = 1;
		$pricing_arr = array();
		foreach ($num_arr as $num) {
		    if ($i % 3 != 0) {
		        $tmp[] = doubleval(str_replace(',', '', $num));
		    } else {
		        $tmp[] = doubleval(str_replace(',', '', $num));
		        $pricing_arr[] = $tmp;
		        unset($tmp);
		    } 
		    $i++;
		}
		// 汇率价格换算 美元-->人民币
		// $market_price_rate (汇率), $profit_rate (利润)
		foreach ($pricing_arr as $key => $val) {
			$pricing_arr[$key][1] = cover_price($val[1]);
			$pricing_arr[$key][3] = cover_price($val[1], 0, true); // 美元*利润
		}
		/*preg_match_all('%<td\sid=quantityavailable.+?>([0-9\.,]+).+?[^\<\/td>]</td>%s', $html, $out);*/
		preg_match_all('%<td\sid=quantityavailable.+?>\D*([0-9\.,]+)\D*</td>%s', $html, $out);
		$goods_number = intval(str_replace(',', '', $out[1][0]));
		$return_arr = array( 'goods_number' => $goods_number, 'pricing' => $pricing_arr, 'time' => $now);
		return $return_arr;
	}
}

/**
 * 获取MOUSER实时价格与库存
 * 当前新的抓取分析。
 * @modify by Langr <hua@langr.org> 2014/04/04 16:37
 * @param string $url
 * @return array
 */
function get_mouser_goods_info ($url) {
	//$pattern = '%http://www.mouser.cn/ProductDetail/.+%';
	$pattern = '%http://cn.mouser.com/ProductDetail/.+%';
	$return_arr = array();
	$now = time_local();
	if (preg_match($pattern, $url)) {
		$goods_info = get_othersite_good_info($url, 2);
		$goods_number = intval(str_replace(',', '', $goods_info['goods_number']));
		$pricing_arr = array();
		foreach ($goods_info['goods_pricing'] as $key => $val) {
			if ($val[1] <= 0) {
				continue;
			}
			$val[0] = intval(str_replace(',', '', $val[0]));
			$pricing_arr[$key][0] = intval($val[0]);
			$pricing_arr[$key][1] = cover_price($val[1], 0, true);
			$pricing_arr[$key][2] = cover_price($val[1], 0, true) * intval($val[0]);
			$pricing_arr[$key][3] = cover_price($val[1], 3, true); // 转成美元
		}
		$return_arr = array( 'goods_number' => $goods_number, 'pricing' => $pricing_arr, 'time' => $now);
		return $return_arr;
	}
}

/**
 * 获取FUTURE商品价格与库存
 * @param string $url
 * @return array
 */
function get_future_goods_info ($url) {
	$pattern = '%http://cn.futureelectronics.com/zh/.+%';
	$return_arr = array();
	$now = time_local();
	if (preg_match($pattern, $url)) {
		$goods_info = get_othersite_good_info($url, 3);
		$goods_number = intval(str_replace(',', '', $goods_info['goods_number']));
		$pricing_arr = array();
		foreach ($goods_info['goods_pricing'] as $key => $val) {
			$pricing_arr[$key][0] = intval($val[0]);
			$pricing_arr[$key][1] = cover_price($val[1], 0, true);
			$pricing_arr[$key][2] = cover_price($val[1], 0, true) * intval($val[0]);
			$pricing_arr[$key][3] = cover_price($val[1], 3, true); // 转成美元
		}
		$return_arr = array( 'goods_number' => $goods_number, 'pricing' => $pricing_arr, 'time' => $now);
		return $return_arr;
	}
}

/**
 * 验证输入的邮件地址是否合法
 * @access  public
 * @param   string      $email      需要验证的邮件地址
 * @return bool
 */
function is_email($user_email)
{
    $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
    if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false) {
        if (preg_match($chars, $user_email)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * 生成分页链接
 * @param integer $offset 
 * @param integer $count
 * @param ineger $limit
 * @param string $url
 * @param string $page_key
 * @return string
 */
function create_page_link($offset = 1, $count, $limit = 20, $url, $page_key = 'p') {
	$return_str = '';
	$roll_page = 5;
	$page_count = ceil($count / $limit);
	$cool_page = ceil($page_count / $roll_page);
	$now_cool_page = ceil($offset / $roll_page);
	if ($page_count > 1) {
		$up_row = $offset - 1;
		$down_row = $offset + 1;
		// 上下翻页字符串
		if ($up_row > 0) {
			$up_page = "<a href='" . $url . "&" . $page_key . "=$up_row' class=\"page-next\">上一页</a>"; 
		} else {
			$up_page = "";
		}
		if ($down_row <= $page_count) {
			$down_page = "<a href='" . $url . "&" . $page_key . "=$down_row' class=\"page-next\">下一页</a>"; 
		} else {
			$down_page = "";
		}
		// << < > >>
		if ($now_cool_page == 1) {
			$the_first = "";
			$pre_page = "";
		} else {
			$pre_row = $offset - $roll_page;
			$pre_page = " ... ";
			$the_first = "<a href='" . $url . "&" . $page_key . "=1' class=\"page-num\" >1</a>";
		}
		if ($now_cool_page == $cool_page) {
			$next_page = "";
			$the_end = "";
		} else {
			$next_row = $offset + $roll_page;
			$the_end_row = $page_count;
			$next_page = " ... ";
			$the_end = "<a href='" . $url . "&" . $p . "=$the_end_row' class=\"page-num\" >" . $the_end_row . "</a>";
		}
		// 1 2 3 4 5 
		$link_page = "";
		for ($i = 1; $i <= $roll_page; $i++) {
			$page = ($now_cool_page - 1) * $roll_page + $i;
			if ($page != $offset) {
				if($page <= $page_count){
                    $link_page .= "<a href='" . $url . "&" . $page_key . "=$page' class=\"page-num\">" . $page . "</a>";
                }else{
                    break;
                }
			} else {
				if ($page_count != 1) {
					 $link_page .= "<strong class='page-cur'>" . $page . "</strong>";
				}
			}
		}
		$return_str = "$up_page $the_first $pre_page $link_page $next_page $the_end $down_page";
	} 
	return $return_str;
}

/**
 * 原价格换算
 * @param float $price 原价格
 * @param integer $exchange_rate_type 0:不作汇率换算 1:美元对人民币  2:人民币对港元 3: 人民币对美元
 * @param boolean $profit_rate_type true:加上利润  false:不加上利润
 * @return float
 $tmp['0']	= $v['Quantity'];	//区间数量
 $tmp['1']	= $v['Price'];	    //单价
 $tmp['2']  = cover_price($v['Price']);//人民币
 $tmp['3']  = cover_price($v['Price'], 0, true);//美元
 */
function cover_price($price = 0, $exchange_rate_type = 1, $profit_rate_type = false) {
	$price = floatval($price);
	$config = S('config');
	$cn_to_hk_exchange_rate = floatval($config['cn_to_hk_exchange_rate']);   // 人民币对港币汇率
	$exchange_rate = floatval($config['exchange_rate']);   // 美元对人民币汇率
	$profit_rate = floatval($config['profit_rate']);   // 利润率
	if ($exchange_rate_type == 1) { // 汇率换算
		$price *= $exchange_rate;
	} elseif ($exchange_rate_type == 2) {
		$price *= $cn_to_hk_exchange_rate;
	} elseif ($exchange_rate_type == 3) {
		$price /= $exchange_rate;
	}
	if ($profit_rate_type) { // 利润
		$price *= $profit_rate;
	}
	return price_format($price);
}

/**
*获取某个商品下面的属性
*@param int goods_id
*/
function get_goods_attr($goods_id = '',$card_code = ''){
	$BigData    = &init_BigData();
	$info	= $BigData->getGoodsAtts(array('hqid'=>$goods_id,'card_code'=>$card_code),'atts','json');
	return $info;
}

/**
 * 获取包装方式
 * @param integer $goods_id
 * @return string
 */
function get_package($goods_id) {
	$return_val = '';
	if ($goods_id) {
		$ext_info = get_goods_attr($goods_id);
		if (is_array($ext_info)) {
			foreach ($ext_info as $key => $val) {
				if (base64_encode(trim($val['attr_name'])) == '5YyF6KOF') {
					$arr = explode('|', $val['ext_value']);   // 过滤以|分割的字符串
					$return_val = trim($arr[0]);
				}
			}
		}
	}
	return $return_val;
}

/**
 * 获取其他站点商品的信息
 *
 * @param integer $site
 * @param string $url
 * @return string
 */
function get_othersite_good_info($url,$site){
	Vendor('MySelf.Snoopy');
	$snoopy = new Snoopy;
	//$snoopy->agent = "(Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.52 Safari/536.5)";
	$snoopy->agent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36";
	$snoopy->rawheaders["Pragma"] = "no-cache";
	$snoopy->fetch($url);
	$html =	$snoopy->results;
	$products = array();
	if($site == 2){
		//preg_match('%<a id="ctl00_ContentMain_hlnk2" class="view-larger".+ href="../../..(.*)" target="_blank">%iUs', $html, $goods_thumb);
		//preg_match('%<span id="ctl00_ContentMain_AlsoBoughtRepeater_ctl03_ManufacturerLabel">(.*)</span>%iUs', $html, $brand_name);
		//preg_match('%<td itemprop="ProductID">\s<h1>(.*)</h1>%iUs', $html, $goods_name);
		//preg_match('%lnkCatalogDataSheet" title="点击以查看此“供应商数据表”。 .+;" href="(.*)" target="_blank">数据表%iUs', $html, $goods_pdf);
		//preg_match('%<td style="border-style:none;padding:0px 4px 0px 0px;" align="right">(.*)</td>%iUs', $html, $goods_number);

		preg_match('%<img id="ctl00_ContentMain_img1".+ src="../../..(.*)" style="border-width:0px;">%iUs', $html, $goods_thumb);
		preg_match('%<a id="ctl00_ContentMain_hlnk10".+>(.*)</a>%iUs', $html, $brand_name);
		preg_match('%<div id="divManufacturerPartNum">\s<h1>(.*)</h1>%iUs', $html, $goods_name);
		preg_match('%<td style="border-style:none;padding:0px 6px 0px 0px;white-space:nowrap;" width="auto" align="right">(.*)</td>%iUs', $html, $goods_number);
		$temp = array();
		$temp['source_url'] = $url;
		if($goods_thumb[1]) { $temp['goods_thumb'] = "http://cn.mouser.com".trim($goods_thumb[1]); }
		$temp['brand_name'] = trim($brand_name[1]);
		$temp['goods_name'] = trim($goods_name[1]);
		//$temp['goods_pdf'] = trim($goods_pdf[1]);
		$temp['goods_number'] = trim($goods_number[1]);
		$temp["goods_pricing"] = $temp["goods_attribute"] = array();
		//preg_match_all('%<td class="PriceBreakQuantity" colspan="1">(.*)</tr>%iUs', $html, $break);
		/* 不向中国区供货的产品取不到数据，或者需要特殊方式取数据 */
		preg_match_all('%<td class="PriceBreakQuantity" colspan="1".+>(.*)</tr>%iUs', $html, $break);
		foreach($break[1] as $breakhtml){
			preg_match('%;">(.*)</a>%iUs', $breakhtml, $num);
			//preg_match('%￥(.*)</span>%iUs', $breakhtml, $price);
			//preg_match('%;">(.*)</a>%iUs', $breakhtml, $num);
			/* 一般为人民币，可能为美元 */
			preg_match('%nowrap">(.+)([0-9\.]*)</span>%iUs', $breakhtml, $price);
			if ( substr($price[1], 0, 1) == '$' ) {
				$price[2] = cover_price($price[2], 1);
			}
			$temp["goods_pricing"][] = array($num[1],$price[2]);			
		}
		/*
		preg_match_all('%<td class="leftcol">(.*)</table>%iUs', $html, $break);
		foreach($break[1] as $breakhtml){
			preg_match('%lblDimension">(.*)</span>%iUs', $breakhtml, $key);
			$key = strip_tags($key[1]);
			if($key){
				preg_match('%lblName">(.*)</span>%iUs', $breakhtml, $price);
				$temp["goods_attribute"][] = array( $key ,strip_tags($price[1]) );
			}
		}*/
	}elseif($site == 3){
		preg_match('%<img id="previewedMEDImage" src="(.*)" imageId="%iUs', $html, $goods_thumb);
		preg_match('%<div id="product-desc" class="green-box">.+<h2>(.*)%iUs', $html, $brand_name);
		preg_match('%<b>制造商零件编号:</b>.+(.*)<br/>%iUs', $html, $goods_name);
		preg_match('%<a id="ctl00_PlaceHolderMain_csDownloadCenter_linkDatasheetUrlJustText" href="(.*)">数据手册</a>%iUs', $html, $goods_pdf);
		preg_match('%现货数量.+<td class="qty">(.*)</td>%iUs', $html, $goods_number);
		preg_match('%manufacturerImage" class="manufacturerImage" src="(.*)" style="%iUs', $html, $brand_logo);
		$temp = array();
		$temp['source_url'] = $url;
		if($goods_thumb[1]) $temp['goods_thumb'] = trim($goods_thumb[1]);
		$temp['brand_name'] = trim($brand_name[1]);
		$temp['brand_logo'] = trim($brand_logo[1]);
		$temp['goods_name'] = trim($goods_name[1]);
		$temp['goods_pdf'] = trim($goods_pdf[1]);
		$temp['goods_number'] = trim($goods_number[1]);
		preg_match_all('%<tr class="price-break"(.*)</tr>%iUs', $html, $break);
		foreach($break[1] as $breakhtml){
			preg_match('%<b>(.*)[-|+|</b>]%iUs', $breakhtml, $num);
			preg_match('%<b><span id=".+">￥(.*)</span></b>%iUs', $breakhtml, $price);
			$temp["goods_pricing"][] = array($num[1],$price[1]);			
		}
	}
	return $temp;
}


/**
处理redis返回值，存储是json格式，最终转化为数组
*/
function json_to_array($string){
	if(is_array($string)){
		return $string;
	}else{
		return json_decode($string,true);
	}
}

/**
*负责处理全站URL的入口
*@param $app 应用URL的应用
*@param $args 所有引用该URL的参数
**/
function build_uri($app = '' ,$args = array()){
	extract($args);
	switch($app){
		case 'Product':
			/* $rec_type =1为团购，0为普通商品 */
			if ($rec_type == '1') {
				return 'Tuan_'.$gid.".html";
			} else {
				$url = "/Product_";
				if($act){
					$url.=$act."_";
				}
				if($gid){
					$url.=$gid;
				}
				if($cid){
					$url.=$cid;
				}
				return $url.".html";
			}
		break;
		case 'Category':
			$url = "/Product_category_id_";
			if($cid){
				$url.=$cid;
			}
			$url.=".html";
			if ($bid) {
				$url.="?bid=".$bid;
			}
			return $url;
		break;
		case 'Help':
			return "/Help_".$id.".html";
		break;
		case 'Article':
			$url = '/news';
			if ($id) {
				$url.="_".$id;
			}
			return $url.".html";
		break;
		case 'Suppliers':
			$url = "/Suppliers";
			if($act){
				$url.="_".$act;
			}
			if ($first_letter){
				$url.="_id_".$first_letter;
			} 
			if ($bid) {
				return $url.=".html?bid=".$bid;
			} else {
				return $url.=".html";
			}
		break;
		case 'Search':
			if (empty($act)) {
				$url = '/Search?';
			} else {
				$url = '/Search_'.$act.'?';
			}
			if ($keyword) {
				$url.="keyword=".$keyword;
			}
			if ($p) {
				$url.="&amp;p=".$p;
			}
			if ($cat_id) {
				$url.="&amp;cid=".$cat_id;
			}
			if ($brand_id){
				$url.="&amp;bid=".$brand_id;
			}
			if ($has_store) {
				$url.="&amp;has_store=1";
			}
			if ($is_rohs) {
				$url.="&amp;is_rohs=1";
			}
			if ($sort) {
				$url.="&amp;sort=".$sort;
			}
			return $url;
		break;
		case 'Exchange':
			$url = '/Exchange_'.$act.'?';
			if ($pmin) {
				$url.='&amp;pmin='.$pmin;
			}
			if ($cid) {
				$url.='&amp;cid='.$cid;
			}
			if ($pmax) {
				$url.='&amp;pmax='.$pmax;
			}
			if ($me) {
				$url.='&amp;me='.$me;
			}
			if ($p) {
				$url.='&amp;page='.$p;
			}
			return $url;
			break;
		case 'Tuan':
			$url = "/Tuan";
			if ($group_id) {
				$url.= '_'.$group_id;
			}
			return $url.=".html";
		break;
	}
}

/**
*取得所有品牌的首字母
@param void 空
@return array() 返回的是数组
*/

function brand_first_letter(){
	$res    = S('brand_first_letter1');
	if(empty($res)){
		echo 3;
		$res     = array();
		$letter = M('brand')->where("brand_desc !=''")->field('first_letter')->group('first_letter')->select();
		
		if($letter){
			foreach($letter as $lk=>$lv){
				if(!empty($lv['first_letter'])){
					$res[] = $lv['first_letter'];
				}
			}
			if ($res) {
				if ($res) {
					foreach ($res as $key => $val) {
						if ($val == 3) {
							unset($res[$key]);
						}
					}
					$res[] = '3';
				}
			}
		}
		
		S('brand_first_letter1',$res);
		return $res;
	}

	return $res;
}

/**
*处理数组到SQL字段的封装
@item_list 字段数组
@field_name 字段名
@return string 返回的是字符串
*/
function db_create_in($item_list, $field_name = '')
{
    if (empty($item_list))
    {
        return $field_name . " IN ('') ";
    }
    else
    {
        if (!is_array($item_list))
        {
            $item_list = explode(',', $item_list);
        }
        $item_list = array_unique($item_list);
        $item_list_tmp = '';
        foreach ($item_list AS $item)
        {
            if ($item !== '')
            {
                $item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
            }
        }
        if (empty($item_list_tmp))
        {
            return $field_name . " IN ('') ";
        }
        else
        {
            return $field_name . ' IN (' . $item_list_tmp . ') ';
        }
    }
}

function time_local()
{
    return (time() - date('Z'));
}

function date_local($format = '' , $time = null){
	
	if ($time === NULL)
    {
        $time = time_local();
    }
    elseif ($time <= 0)
    {
        return '';
    }

	$time += (8 * 3600);
	return date($format, $time);
}

/**
 * 取得商品的价格，通过商品id
 *
 * @access  public
 * @param   $goods_id   商品id
 * @return  array		价格数组
 */
 function get_goods_price($goods_id){
	$price			   = array();
	$BigData = &init_BigData();
	$info    = $BigData->BigDataGoodsPrice($goods_id);
	
	if ($info['Result']) {
		foreach ($info['Result'] as $k => $v) {
			$tmp['0']	= $v['Quantity'];				//区间数量
			$tmp['1']	= $v['Price'];					//单价
			$tmp['2']   = cover_price($v['SalePrice']); //人民币
			$tmp['3']   = $v['SalePrice'];			    //美元
			$price[] = $tmp;
		}
	}
	return $price;
 }

 /**
 * 取得商品的库存，通过商品id
 *
 * @access  public
 * @param   $goods_id   商品id
 * @return  array	价格数组
 */
 function get_goods_number($goods_id){
	$BigData= &init_BigData();
	$res	= $BigData->BigDataGoodsNumber($goods_id,'goods_number','json');
	
	return $res;
 }

 /**
 * 获得主物料信息
 *
 * @access  public
 * @param   $goods_id   商品id
 * @return  array		价格数组
 */
function get_item_info($item_id) {
	
	$BigData= &init_BigData();
	$res	= $BigData->BigDataItemInfo($item_id,'item_info','json');
	
	return $res;
}

 /**
 * 短信发送接口
 *
 * @access  public
 * @param   $mobile   手机号码
 * @param   $msg      短信发送的内容
 * @return  boolean	  是否发送成功，1成功，0失败
 */


function send_short_msg($mobile = '' , $msg = ''){
     
	$flag = 0; //183.62.238.146
	$argv = array( 
			 'ClientName'=>real_ip(), ////替换成您自己的序列号
			 //'ClientName'=>'183.62.107.205', ////替换成您自己的序列号
			 'TelNumbers'=> $mobile, 
			 'SendMessage'=>$msg,
			 'ProjectCode'=>'KITSMALL',//短信内容
			 'SmsDescribe'=>'华强联大短信发送',
			 //'UserIp'     =>'183.62.107.205',
			 'UserIp'     =>real_ip(),
	  ); 
	
	 foreach ($argv as $key=>$value) { 
		  if ($flag!=0) { 
						 $params .= "&"; 
						 $flag = 1; 
		  } 
		 $params.= $key."="; $params.= urlencode($value); 
		 $flag = 1; 
	 } 
	
	 $ch = curl_init();        
     curl_setopt($ch, CURLOPT_HEADER,0);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_URL, 'http://smsapi.hqew.com/SmsService.aspx');
     curl_setopt($ch, CURLOPT_POST, 1);
	 curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
     $res = curl_exec($ch);;
     curl_close($ch);
	
     $res2 = json_decode($res,true); 
	 
	 if ($res2['Result']) {
		$res3 = json_decode($res2['Result'],true);
		if ($res3['ResultStatus'] == 1) {
			return 1;	//发送成功
		} 
		return 0;
	 }
	 
	 return 0;
}

/**
 * 验证手机号码格式
 *
 * @access  public
 * @param   $mobile   手机号码
 * @return  boolean	  是否正常的手机号码格式
 */
function valid_mobile($mobile) {
	
	return preg_match("/^1\d{10}$/is",$mobile);
}

/**
 * 生成查询订单总金额的字段
 * @param   string  $alias  order表的别名（包括.例如 o.）
 * @return  string
 */

function order_amount_field($alias = '')
{
    return "   {$alias}goods_amount + {$alias}tax + {$alias}shipping_fee" .
           " + {$alias}insure_fee + {$alias}pay_fee + {$alias}pack_fee" .
           " + {$alias}card_fee ";
}

/**
 * 通过时间段查询订单
 * @param   int  $otime   时间段类型
 * @return  string sql    通过时间段来筛选不同时间段的订单
 <option value="0">全部时间</option>
 <option value="1">1个月内</option>
 <option value="2">半年内</option>
 <option value="3">1年内</option>
 <option value="4">1年前</option>
 */
 function query_order_by_time($otime = 0,$prefix = '') {
	
	$sql	= "";
	$time	= time_local();
	switch($otime) {
		case '0':
		break;
		case '1':
			$start = time_local()-3600*24*30;
			$sql=" and ".$prefix."add_time >$start and add_time < $time";
		break;
		case '2':
			$start = time_local()-3600*24*30*6;
			$sql=" and ".$prefix."add_time >$start and add_time < $time";
		break;
		case '3':
			$start = time_local()-3600*24*30*12;
			$sql=" and ".$prefix."add_time >$start and add_time < $time";
		break;
		case '4':
			$start = time_local()-3600*24*30*12;
			$sql=" and ".$prefix."add_time < $start ";
		break;
		default :
		break;
	}

	return $sql;
 }

/**
 * thinkphp excel解析excel
 *@param  $filename  string excel文件地址
 *@return $array	 array  返回excel文件内容
 */
 function explain_excel($filename = '') {
	
	Vendor('phpexcel.PHPExcel.IOFactory');
	$reader = PHPExcel_IOFactory::createReader('Excel2007'); //设置以Excel5格式(Excel97-2003工作簿)
	if(!$reader->canRead($filename)){ 
		$reader = PHPExcel_IOFactory::createReader('Excel5');
		if(!$reader->canRead($filename)){ 
		echo 'no Excel'; 
		return ; 
		} 
	} 
	$PHPExcel = $reader->load($filename); // 载入excel文件
	$sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
	$highestRow = $sheet->getHighestRow(); // 取得总行数
	$highestColumm = $sheet->getHighestColumn(); // 取得总列数
	$array		= array();
	$i			= 0;
	/** 循环读取每个单元格的数据 */
	for ($row = 2; $row <= $highestRow; $row++){//行数是以第1行开始
		for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
			
			if ($column == 'F') {
				$val = $sheet->getCell($column.$row)->getValue();
				if (preg_match('/\d{5}/',$val)) {
					$array[$i][]=gmdate("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($val));
				} else {
					$array[$i][] = $sheet->getCell($column.$row)->getValue();
				} 
			} else {
				$array[$i][] = $sheet->getCell($column.$row)->getValue();
			}
			
		}
		$i++;
	}
	
	return $array;
 }

/**
 * 检测全站交易货币，以及交易地点的种类
 * @param integer $goods_type  交易地点类型，货币类型，1为人民币，2为美元
 * @return bool     是否合法
 */
function check_goods_type($goods_type = 1) {
	
	if (in_array($goods_type,array(1,2))) {
		return 1;
	} else {
		return 0;
	}
}

/**
 * 获取支付名称
 * @param integer $pay_id
 * @return string
 */
function get_pay_name ($pay_id = 1) {
	$payment = array(
			'1' => '支付宝',
			'2' => '财富通',
			'3' => '公司转账',
			'4' => '公司转账',
			'5' => '余额支付',
			'6' => '网银支付',
			'7' => '邮局汇款',
		);
	return $payment[$pay_id];
}

/*
* 取得网银支付的银行名称
*/
function get_union_bank_name($bank_type = '',$pay_id = '') {	
	if ($pay_id == 6) {
		$type_union = array(
			'09' =>	'兴业银行','12' => '民生银行','13' =>'华夏银行','14' =>	'深发展银行','15'=>'北京银行','16'=>'浦发银行','21'=>'交通银行','25'=>'工商银行',
			'27' => '建设银行','28'=>'招商银行','29'=>'农业银行','33'=>'中信银行','36'=>'光大银行','40'=>'北农商','45'=>'中国银行','48'=>'东亚银行',
			'49'=>'南京银行','50'=>'平安银行','51'=>'杭州银行','52'=>'宁波银行','53'=>'浙商','54'=>'上海银行','55'=>'渤海银行',
			'61'=>'PNR钱管家','69'=>'上农商B2C','70'=>'工行B2B','71'=>'农行B2B','72'=>'建行B2B','74'=>'光大银行B2B','75'=>'北农商B2B',
			'76'=>'浦发B2B','78'=>'招行B2B','81'=>'深发B2B','B3'=>'B广发','B8'=>'B邮储'
		);
		return $type_union[$bank_type];
	}
}

/**
 * 最小询价数量
 * @param  array $price 价格梯度
 * @return int          最小购买数量
 */
function min_inquire_num(&$price = array()) {	
	if (empty($price)) return 1;
	else
		return $price[(count($price)-1)]['0'];
}

/**
 * 获取某个型号的图片
 * @param  string  $goods_name  型号
 * @param  string  $small		图片类型，small小图片，big大图片
 * @return string  $first_word
 */
function get_goods_thumb($goods_name = '',$type = 'small') {
	if (empty($goods_name)) {
		if ($type == 'small') {
			return C('NO_GOODS_THUMB');
		} else {
			return C('NO_GOODS_IMG');
		}
	} else {
		$filename = md5(strip_tags($goods_name));	
		if ($type == 'small') {
			return  "http://img.kitsmall.com/small/".substr($filename,0,2)."/".substr($filename,2,2)."/".$filename.".jpg";
		} else {
			return  "http://img.kitsmall.com/big/".substr($filename,0,2)."/".substr($filename,2,2)."/".$filename.".jpg";
		}
	}
}

 /**
 * 使用CURL模拟post
 * 返回结果string
 * @access  public
 * @param   string       $url 请求地址
 * @return  void
 */
function curl_post($url = '' , $args = '') {
	$ch = curl_init(); //初始化curl
	curl_setopt($ch, CURLOPT_URL, $url);//设置链接
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
	curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
	curl_setopt($ch, CURLOPT_POSTFIELDS,$args );//POST数据
	$output = curl_exec($ch);//接收返回信息
	curl_close($ch); //关闭curl链接
	return $output;//显示返回信息
}

/** 
 * @fn
 * @brief 数字货币转化为中文大写
 * @access public
 * @param string $money	数字格式货币
 * @return  中文格式货币
 */  
function cny_cn($money) { /* {{{ */
	$money = sprintf("%01.2f", $money);
	if ($money <= 0) {
		return '零圆';
	}
	$units = array ( '', '拾', '佰', '仟', '', '万', '亿', '兆' );
	$amount = array( '零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖' );
	$arr = explode('.', $money);
	$money = strrev($arr[0]);			/* 翻转整数 */
	$length = strlen($money);
	for ($i = 0; $i < $length; $i++) {
		$int[$i] = $amount[$money[$i]];		/* 获取大写数字 */
		/* 去掉连继零 */
		if ( $i > 0 && $money[$i-1] == 0 && $money[$i] == 0 ) {
			$int[$i] = '';
		}
		/* (金额超过10) 圆，万，亿，兆 前零不读 */
		if ( ($i == 0 || $i == 4 || $i == 8) && $money[$i] == 0 ) {
			$int[$i] = '';
		}

		if (!empty($money[$i])) {  
			$int[$i] .= $units[$i%4];	/* 获取整数位 */
		}
		if ($i%4 == 0) {
			$int[$i] .= $units[4+floor($i/4)];	/* 取整 */
		}
	}
	$con = isset($arr[1]) ? '圆' . $amount[$arr[1][0]] . '角' . $amount[$arr[1][1]] . '分' : '圆整';  
	return implode('', array_reverse($int)) . $con;	/* 整合数组为字符串 */
} /* }}} */

/**
 * @fn
 * @brief 日志记录函数
 * @param $log_file	日志文件名
 * @param $log_str	日志内容
 * @param $show		日志内容是否show出
 * @param $log_size	日志文件最大大小，默认20M
 * @return void
 */
function wlog($log_file, $log_str, $show = false, $log_size = 20971520) /* {{{ */
{
	ignore_user_abort(TRUE);

	$time = '['.date('Y-m-d H:i:s').'] ';
	if ( $show ) {
		echo $time.$log_str.((PHP_SAPI == "cli") ? "\r\n" : "<br>\r\n");
	}
	if ( empty($log_file) ) {
		$log_file = 'wlog.txt';
	}
	if ( defined('APP_LOG_PATH') ) {
		$log_file = APP_LOG_PATH.$log_file;
	}

	if ( !file_exists($log_file) ) { 
		$fp = fopen($log_file, 'a');
	} else if ( filesize($log_file) > $log_size ) {
		$fp = fopen($log_file, 'w');
	} else {
		$fp = fopen($log_file, 'a');
	}

	if ( flock($fp, LOCK_EX) ) {
		$cip	= defined('CLIENT_IP') ? '['.CLIENT_IP.'] ' : '['.getenv('REMOTE_ADDR').'] ';
		$log_str = $time.$cip.$log_str."\r\n";
		fwrite($fp, $log_str);
		flock($fp, LOCK_UN);
	}
	fclose($fp);

	ignore_user_abort(FALSE);
} /* }}} */

/**
 * @fn
 * @brief 通过http协议模拟浏览器向服务器请求数据
 * 	此函数有超级牛力^^
 * 	支持并独立保存COOKIE, 支持HTTP/1.1 Transfer-Encoding: chunked.
 * 	向 $url 以 POST 方式传递数据, 支持 ssl 和 http 方式, 支持 http 帐号验证.
 * 	TODO: 将 cookie 保存为 json 格式, 或者以行记录.
 * 	NOTE: 如果服务器端 Accept-Encoding 默认不为 identity, 
 * 		请发送 Accept-Encoding 头, 确保返回的消息不被编码:
 * 		"Accept-Encoding: " or "Accept-Encoding: identity"
 * @author Langr<hua@langr.org>
 * @param $url		
 * @param $data		POST 到对方的数据, 为空时以 GET 方式传递, e.g. array('n1'=>'v1','n2'=>'v2') or "n1=v1&n2=v2"
 * @param $header	http header 头数据, e.g. array('User-Agent'=>'Mozilla/5.0') or "User-Agent: Mozilla/5.0\r\n"
 * @param $cookie_path	cookie存储的路径, 为 'nonsupport' 时则不存储, 
 * 			如果定义了 'H2H_COOKIE_PATH' 常量, 则不关注 'nonsupport' 值, 全部存储.
 * @param $debug	调试: false 不调试, true 调试, 记录发送接收头到日志文件.
 * 			如果定义了 'APP_DEBUG' 常量, 则不关注此参数.
 * @return $url 传回的web数据
 */
function http2host($url, $data = array(), $header = "User-Agent: Mozilla/5.0 (Windows NT 6.1) Chrome/33.0\r\n", $cookie_path = 'nonsupport', $debug = false) { /* {{{ */
	$encoded = '';
	$post = 'POST';
	$line = '';

	if ( defined('APP_DEBUG') ) {
		$debug = APP_DEBUG;
	}
	/* 准备数据 */
	if ( is_array($data) && count($data) > 0 ) {
		while ( list($k, $v) = each($data) ) {
			$encoded .= rawurlencode($k)."=".rawurlencode($v)."&";
		}
		$encoded = substr($encoded, 0, -1);
	} else if ( is_string($data) ) {
		$encoded = $data;
	} else {
		$post	= 'GET';
	}

	$urls = parse_url($url);
	if ( !isset($urls['port']) ) { $urls['port'] = 80; }
	if ( !isset($urls['query']) ) { $urls['query'] = ''; }
	if ( !isset($urls['path']) ) { $urls['path'] = '/'; }
	if ( !isset($urls['host']) ) { return '-11 url error'; }

	$m = '';
	if ( $urls['scheme'] == 'https' ) {
		$m = 'ssl://';
		$urls['port'] = ($urls['port'] == 80) ? 443 : $urls['port'];
	}
	if ( ($urls['scheme'] == 'ssl' || $urls['scheme'] == 'udp') ) {
		$m = $urls['scheme'].'://';
	}
	$fp = @fsockopen($m.$urls['host'], $urls['port']);
	if ( !$fp ) {
		return "-12 failed to open socket to {$urls['host']}:{$urls['port']}";
	}

	/* request */
	$request_headers = '';
	//$request_headers .= sprintf($post." %s%s%s HTTP/1.1\r\n", $urls['path'], $urls['query'] ? '?' : '', $urls['query']);
	$request_headers .= sprintf($post." %s%s%s HTTP/1.0\r\n", $urls['path'], $urls['query'] ? '?' : '', $urls['query']);
	$request_headers .= "Host: {$urls['host']}\r\n";
	/* basic 认证 */
	if ( !empty($urls['user']) ) {
		$request_headers .= "Authorization: Basic ".base64_encode($urls['user'].':'.$urls['pass'])."\r\n";
	}
	if ( $post == 'POST' ) {
		$request_headers .= "Content-type: application/x-www-form-urlencoded; charset=utf-8\r\n";
		$request_headers .= "Content-length: ".strlen($encoded)."\r\n";
	}
	/* 自定义 header */
	if ( is_array($header) && count($header) > 0 ) {
		while ( list($k, $v) = each($header) ) {
			$request_headers .= "$k: $v\r\n";
		}
	} else if ( is_string($header) ) {
		$request_headers .= $header;
	}

	/* COOKIE 支持, send */
	$_allow_cookie = true;
	if ( defined('H2H_COOKIE_PATH') ) {
		$cookie_path = H2H_COOKIE_PATH;
	} else if ( $cookie_path == 'nonsupport' ) {
		$_allow_cookie = false;
		$cookie_path = '';
	}
	$cookie_file = $cookie_path.$urls['host'].'.cookie';
	if ( $_allow_cookie && file_exists($cookie_file) ) {
		/* TODO: json */
		$request_headers .= "Cookie: ".trim(file_get_contents($cookie_file))."\r\n";
	}
	$request_headers .= "Connection: close\r\n\r\n";

	if ( $post == 'POST' ) {
		$request_headers .= "$encoded\r\n";
	}
	if ( $debug ) {
		wlog('http2host.log', "Host[$m{$urls['host']}:{$urls['port']}] URL[$url]\r\n".$request_headers);
	}
	fputs($fp, $request_headers);

	/* response */
	$response_headers = '';
	$line = fgets($fp, 4096);
	$response_headers .= $line;
	/* http error? 3xx 不处理 */
	if ( !preg_match("/^HTTP\/1\.. 200/", $line) ) {
		$_errno = substr(trim($line), 9);
		if ( $_errno[0] != '3' ) {
			return '-'.$_errno;
		}
	}

	$results = '';
	$inheader = true;
	$i = 0;
	$cookie_o = array();
	$cookie_n = array();
	$has_chunk = false;	/* 数据分块的？ */
	while ( !feof($fp) ) {
		$line = fgets($fp, 4096);
		if ( $line === false ) {
			break;
		}
		if ( $inheader ) {
			$response_headers .= $line;
		}
		if ( $inheader && substr($line, 0, 19) == 'Transfer-Encoding: ' ) {
			if ( trim(substr($line, 19)) == 'chunked' ) {
				$has_chunk = true;
			}
		}
		/* COOKIE 支持, recv */
		if ( $inheader && $_allow_cookie && substr($line, 0, 12) == 'Set-Cookie: ' ) {
			$line = substr(trim($line), 12);
			$cookie_o = $cookie_n = array();
			/* TODO: json */
			if ( file_exists($cookie_file) ) {
				$cookie_old = trim(file_get_contents($cookie_file));
				$cookie_array = explode('; ', $cookie_old);
				foreach ( $cookie_array as $k=>$v ) {
					$eq = strpos($v, '=');
					if ( $eq === false ) continue;
					$cookie_o[substr($v, 0, $eq)] = substr($v, $eq + 1);
				}
			}
			$cookie_new = explode('; ', $line);
			$eq = strpos($cookie_new[0], '=');
			$cookie_n[substr($cookie_new[0], 0, $eq)] = substr($cookie_new[0], $eq + 1);

			$cookie_n = array_merge($cookie_o, $cookie_n);
			$line = '';
			foreach ( $cookie_n as $k=>$v ) {
				$line .= $k.'='.$v.'; ';
			}
			$line = substr($line, 0, -2);
			file_put_contents($cookie_file, $line);
		}
		/* 去掉第一次的空行 */
		if ( $inheader && ($line == "\n" || $line == "\r\n") ) {
			$inheader = false;
			break;
		}
	}
	/* line 1 */
    	$_data = fgets($fp, 4096);
	$r = trim($_data);
	$rn = 0;		/* 读块长度 */
	/* HTTP/1.1 Transfer-Encoding: chunked 支持，正文中的块长度标识 */
	if ( $has_chunk && is_numeric('0x'.$r) ) {
		$rn = base_convert($r, 16, 10);
		wlog('http2host.log', "length chunk:$r,$rn,total:".strlen($results));
		$_data = fgets($fp, 4096);	/* has_chunk 去掉第一行 \r or \r\n */
		if ( $_data != "\n" && $_data != "\r\n" ) {
			$results .= $_data;
			$rn -= strlen($_data);
		}
	} else {
    		$results .= $_data;
	}
	do {
		/* 读块 */
		if ( $has_chunk && $rn > 0 ) {
			$__tmp = '';
			while ( ($__tmp = fread($fp, $rn)) !== false ) {
				$rn = $rn - strlen($__tmp);
    				$results .= $__tmp;
				if ( $rn == 0 ) { break; }
			}
		}
    		$_data = fgets($fp, 4096);
    		if ( $_data === false ) {
        		break;
		}
		/* 取块长度 */
		if ( $has_chunk ) {
			if ( $_data != "\n" && $_data != "\r\n" ) {
				$rn = base_convert(trim($_data), 16, 10);
				wlog('http2host.log', "length chunk:".trim($_data).",$rn,total:".strlen($results));
			}
			continue;
		}
		$results .= $_data;
	} while ( !feof($fp) );
	fclose($fp);
	if ( $debug ) {
		wlog('http2host.log', "\r\n".$response_headers);
	}

	return $results;
} /* }}} */

/**
 * @fn
 * @brief 去掉url路径中的'../'，并返回正确的路径
 * @param 
 * @return 
 */
function format_url($url) /* {{{ */
{
	if ( substr($url, 0, 4) != 'http' ) { 
		$url = 'http://'.$url;
	}
	$http_arr = parse_url($url);
	$arr = explode("/", $http_arr['path']);
	for ( $i=0; $i < count($arr); $i++ ) {
		if ( $arr[$i] == '..' ) {
			if ( $i == 0 ) {
				array_shift($arr);
			} else {
				array_splice($arr, $i - 1, 2);
			}
			$i = -1;
			continue;
		}
		if ( $arr[$i] == '' ) {
			array_splice($arr, $i, 1);
		}
	}
	$new_url = join("/", $arr);
	$new_query = empty($http_arr['query']) ? '' : '?'.$http_arr['query'];
	$new_url = $http_arr['scheme'].'://'.$http_arr['host'].'/'.$new_url.$new_query;
	return $new_url;
} /* }}} */

/**
 * @fn
 * @brief 连接url，处理一个页面中的href连接，
 * 	同时需处理url中的path, ../, args...
 * @param $purl	父url, 
 * @param $surl	子url, 
 * @return 
 */
function href_url($purl, $surl)
{
	if ( substr($surl, 0, 4) == 'http' ) {
		return $surl;
	}
	if ( substr($purl, 0, 4) != 'http' ) { 
		$purl = 'http://'.$purl;
	}
	$p = parse_url($purl);
	$p['path'] = empty($p['path']) ? '/' : $p['path'];
	$surl = ($surl[0] == '/' ? $p['scheme'].'://'.$p['host'].$surl
		: (substr($p['path'], -1) == '/' ? $p['scheme'].'://'.$p['host'].$p['path'].$surl 
			: $p['scheme'].'://'.$p['host'].$p['path'].'/../'.$surl));
	$surl = format_url($surl);
	return $surl;
}

/**
 * @fn
 * @brief 数组转换为xml
 * @param 
 * @return 
 */
function array2xml(&$data = array()) /* {{{ */
{
	$xml = "<?xml version='1.0' encoding='UTF-8' ?>";
	return $xml._array2xml($data);
} /* }}} */

/**
 * @brief 数组转换为xml
 */
function _array2xml(&$data = array()) /* {{{ */
{
	$xml = '';
	foreach ( $data as $key => $val ) {
		if ( is_array($val) ) {
			$xml .= "<$key>"._array2xml($val)."</$key>";
		} else {
			$xml .= "<$key>$val</$key>";
		}
	}
	return $xml;
} /* }}} */
