/** 
 * @file Gps2Json.java
 * @brief GPS坐标/基站定位信息 储存为json
 * 	[timestamp,lng(纬度),lat(经度),height(离地平线高度)],
 * 	纬度N（北纬）或S（南纬） 用正负号区别
 * 	经度E（东经）或W（西经）
 *
 * 	TODO: GPS to KML(使用Google Earth等没有偏差加密的标准地图显示轨迹)
 *
 * 	Gps2Json 格式为1: array-array.
 * 	1. array-array: [[time,n,e,h],[time,n,e,h],[time,n,e,h]]
 * 	2. array-map: [{"time":time,"n":n,"e":e},{"time":time,"n":n,"e":e},{"time":time,"n":n,"e":e}]
 * 	3. map-array: {"id1":[time,n,e],"id2":[time,n,e],"id3":[time,n,e]}
 * 	4. map-map: {"id1":{"time":time,"n":n,"e":e},"id2":{"time":time,"n":n,"e":e},"id3":{"time":time,"n":n,"e":e}}
 *
 * 	map-array: {"filename":[90,1],"filename2":[90,3],"filename3":[90,1]}
 * 	map-map: {"filename":{"rotate":90,"count":1},"filename2":{"rotate":90,"count":3},"filename3":{"rotate":90,"count":1}}
 * 
 * Copyright (C) 2015 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package util
 * @author Langr <hua@langr.org> 2015/01/07 18:16
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: Gps2Json.java 57 2015-01-13 03:58:30Z loghua@gmail.com $
 */

package org.langr.util;

import java.util.*;
import java.io.*;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.*;

import android.annotation.SuppressLint;
import android.content.Context;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.os.Bundle;
import android.telephony.TelephonyManager;
import android.telephony.gsm.GsmCellLocation;
import android.util.Log;

public class Gps2Json
{
	/* 保存文件名 */
	private String mFilename = null;
	private JSONArray mJlist = null;

	public Gps2Json() {
		this("org.langr.gps.txt");
	}

	public Gps2Json(String filename) {
		mFilename = filename;
	}

	/**
	 * 获取gps记录值
	 */
	public JSONArray getGps()
	{
		return getGps(mFilename);
	}

	public JSONArray getGps(String filename)
	{
		byte[] data = null;
		String json = null;

		data = readFile2(filename);
		if ( data != null ) {
			data[data.length-1] = ']';
			json = "["+new String(data);
		} else {
			return new JSONArray();
		}
		
		JSONArray jlist = null;
		try {
			jlist = new JSONArray(json);
		} catch (Exception e) {
			jlist = new JSONArray();
		}

		return jlist;
	}

	/**
	 * 添加gps记录值
	 * @param gps_lat	经度
	 * @param gsp_lng	纬度
	 * @param height	高度
	 */
	public int appendGps(double gps_lng, double gps_lat, double height)
	{
		return appendGps(gps_lng, gps_lat, height, mFilename);
	}

	public int appendGps(double gps_lng, double gps_lat, double height, String filename)
	{
		byte[] data = null;
		long time = Calendar.getInstance().getTimeInMillis();

		data = ("["+time+","+gps_lat+","+gps_lng+","+height+"],").getBytes();
		/* 追加 */
		writeFile2(filename, data, true);
		return 0;
	}

	/**
	 * 写文件，二进制适用
	 * @param append true 追加
	 */
	public boolean writeFile2(String filename, byte[] data)
	{
		return writeFile2(filename, data, true);
	}

	public boolean writeFile2(String filename, byte[] data, boolean append)
	{
		//byte[] data = String data.getBytes();
		OutputStream f = null;
		try {
			f = new FileOutputStream(filename, append);
			//f.write(data, 0, data.length);
			f.write(data);
			f.close();
		} catch (Exception e) {
			if (f != null) {
				try {
					f.close();
				} catch (Exception e2) {
				}
			}
			//e.printStackTrace();
			return false;
		}
		return true;
	}

	/**
	 * 读取文件内容，二进制适用
	 */
	public byte[] readFile2(String filename)
	{
		byte[] readBuf = null;
		int size = 0;
		int offset = 0;
		int r = 0;	/* */
		InputStream f = null;
		try {
			f = new FileInputStream(filename);
			/* 可读大小 */
			size = f.available();
			readBuf = new byte[size];
			while (size > 0 && r >= 0) {	/* -1 文件结束？ */
				//(offset<bytes.length&&(numRead=is.read(bytes,offset,bytes.length-offset))>=0 );
				r = f.read(readBuf, offset, size);
				//System.out.println("read:"+filename+" size:"+size+" offset:"+offset+" read:"+r);
				offset += r;
				size -= r;
			}
			f.close();
		} catch (Exception e) {
			/* error */
			if (f != null) {
				//System.out.println("read error:"+filename);
			}
			//e.printStackTrace();
		}
		//return String ret = EncodingUtils.getString(readBuf, "UTF-8");
		return readBuf;
	}

	/**
	 * 以下用于基站定位
	 */

	/* 基站信息结构体, 暂时只支持GSM */
	public class LCell {
		public int MCC;
		public int MNC;
		public int LAC;
		public int CID;
		public String type = "gsm";
		public String imsi = null;
		//public String imei = null;
	}
	 
	/* 经纬度信息结构体 */
	public class LGps {
		public double latitude;
		public double longitude;
		public double height;
		public long time;
	}

	/**
	 * 获取基站信息
	 */
	private LCell getCellInfo(Context c)
	{
		LCell cell = new LCell();
 
		/* 调用API获取基站信息 */
		TelephonyManager mTel = (TelephonyManager) c.getSystemService(Context.TELEPHONY_SERVICE);
		GsmCellLocation location = (GsmCellLocation) mTel.getCellLocation();
		String imei = mTel.getDeviceId();	/* IMEI */
		cell.imsi = mTel.getSubscriberId();	/* SIM卡号 */
		String mtype = android.os.Build.MODEL;	/* 手机型号 */
		String mtyb= android.os.Build.BRAND;	/* 手机品牌 */
		//String numer = mTel.getLine1Number();	/* 手机号码，有的可得，有的不可得 */
		Log.i("langr", "IMEI:"+imei+",IESI:"+cell.imsi+",型号:"+mtype+",品牌:"+mtyb);
		if (location == null) {
			Log.i("langr", "get CellInfo error.");
			return cell;
		}
 
		String operator = mTel.getNetworkOperator();
		Log.i("langr", "get CellInfo:"+operator);
		if (operator == null || operator.length() == 0) {
			return cell;
		}
		cell.MCC = Integer.parseInt(operator.substring(0, 3));	/* 国家代码 */
		cell.MNC = Integer.parseInt(operator.substring(3));	/* 网络代码 */
		cell.LAC = location.getLac();				/* 区域代码 */
		cell.CID = location.getCid();				/* 基站编号 */
 
		return cell;
	}
	 
	/**
	 * 获取基站经纬度
	 * 高德API KEY:
	 * 933ce53d9db0003b7381e0be689bbf05
	 * 1c555e3062626c4c7dc4cb7eeaf0addf
	 */
	@SuppressLint("SdCardPath") 
	private LGps getCellGpsGoogle(LCell cell)
	{
		LGps gps = new LGps();
		if (cell == null) {
			//return gps;
		}
		writeFile2("/sdcard/org.langr/org.langr.txt", ("\ncell: mcc:"+cell.MCC+" mnc:"+cell.MNC+" lac:"+cell.LAC+" cid:"+cell.CID).getBytes());
		/* 采用Android默认的HttpClient */
		HttpClient client = new DefaultHttpClient();
		/* 采用POST方法 */
		//HttpPost post = new HttpPost("http://www.google.com/loc/json");
		//HttpPost post = new HttpPost("http://www.jizhanyun.com");
		HttpPost post = new HttpPost("http://www.minigps.net/minigps/map/google/location");
		try {
			/* 构造POST的JSON数据 */
			JSONObject holder = new JSONObject();
			holder.put("version", "1.1.0");
			holder.put("host", "maps.google.com");
			holder.put("address_language", "zh_CN");
			holder.put("request_address", true);
			holder.put("radio_type", "gsm");
			holder.put("carrier", "HTC");

			JSONObject tower = new JSONObject();
			tower.put("mobile_country_code", cell.MCC);
			tower.put("mobile_network_code", cell.MNC);
			tower.put("cell_id", cell.CID);
			tower.put("location_area_code", cell.LAC);

			JSONArray towerarray = new JSONArray();
			towerarray.put(tower);
			holder.put("cell_towers", towerarray);
			writeFile2("/sdcard/org.langr/org.langr.txt", ("\npost:"+holder.toString()).getBytes());
			StringEntity query = new StringEntity(holder.toString());
			post.setEntity(query);
 
			/* 发出POST数据并获取返回数据 */
			HttpResponse response = client.execute(post);
			HttpEntity entity = response.getEntity();
			BufferedReader buffReader = new BufferedReader(new InputStreamReader(entity.getContent()));
			StringBuffer strBuff = new StringBuffer();
			String result = null;
			while ((result = buffReader.readLine()) != null) {
				strBuff.append(result);
			}
 
			writeFile2("/sdcard/org.langr/org.langr.txt", ("\nhttp return:"+strBuff.toString()).getBytes());
			/* 解析返回的JSON数据获得经纬度 */
			JSONObject json = new JSONObject(strBuff.toString());
			JSONObject subjosn = new JSONObject(json.getString("location"));
 
			gps.latitude = Double.valueOf(subjosn.getString("latitude"));
			gps.longitude = Double.valueOf(subjosn.getString("longitude"));
				 
			Log.i("langr", "cell gps:"+gps.latitude + gps.longitude);
		} catch (Exception e) {
			Log.e("langr", e.getMessage()+e.toString());
		} finally {
			post.abort();
			client = null;
		}
		 
		return gps;
	}

	/**
	 * url: http://www.cellid.cn/cidInfo.php?lac=9515&cell_id=51014
	 * ret: cidMap(22.52430678,113.9244069,'(9515,51014)	22.52430678,113.9244069<br>广东省深圳市南山区桂庙路南山大道2002号') 
	 */
	@SuppressLint("SdCardPath") 
	private LGps getCellGps(LCell cell)
	{
		LGps gps = new LGps();
		if (cell == null) {
			//return gps;
		}
		//writeFile2("/sdcard/org.langr/org.langr.txt", ("\ncell: mcc:"+cell.MCC+" mnc:"+cell.MNC+" lac:"+cell.LAC+" cid:"+cell.CID).getBytes());
		String url = "http://www.cellid.cn/cidInfo.php?lac="+cell.LAC+"&cell_id="+cell.CID;
		String ret = post2url(url, null);
		writeFile2("/sdcard/org.langr/org.langr.txt", ("\nhttp return:"+ret).getBytes());
		if ( ret == null || ret.length() < 1 ) {
			return gps;
		}
		int index = ret.indexOf(',');
		gps.latitude = Double.parseDouble(ret.substring(7, index));
		int index2 = ret.indexOf(',', index+1);
		gps.longitude = Double.parseDouble(ret.substring(index+1, index2));
		return gps;
	}

	public String post2url(String url, String data)
	{
		StringBuffer strBuff = new StringBuffer();
		HttpClient client = new DefaultHttpClient();
		HttpPost post = new HttpPost(url);
		try {
			if (data != null && data.length() > 0) {
				StringEntity query = new StringEntity(data);
				post.setEntity(query);
			}
			/* 发出POST数据并获取返回数据 */
			HttpResponse response = client.execute(post);
			HttpEntity entity = response.getEntity();
			BufferedReader buffReader = new BufferedReader(new InputStreamReader(entity.getContent()));

			String result = null;
			while ((result = buffReader.readLine()) != null) {
				strBuff.append(result);
			}
		} catch (Exception e) {
			Log.e("langr", e.getMessage()+e.toString());
		} finally {
			post.abort();
			client = null;
		}
		return strBuff.toString();
	}
	
	/**
	 * 获取大致(基站网络)位置
	 */
	public LGps getCellLocation(Context c)
	{
		return getCellGps(getCellInfo(c));
	}

	/**
	 * 获取当前位置
	 * 有精确位置就取精确位置，没精确位置就取大致位置
	 */
	public LGps getCurrentGps(Context c) 
	{
		LGps gps = null;
		gps = getGpsLocation(c);
		if ( gps == null ) {
			return getCellLocation(c);
		}
		return gps;
	}

	private LocationManager locationManager;
	/**
	 * 获取精确(GPS)位置
	 * 默默的，只获取最后一次的位置，没有就返回空
	 */
	public LGps getGpsLocation(Context c)
	{
		return getGpsLocation(c, locationListener);
	}

	@SuppressLint("SdCardPath") 
	public LGps getGpsLocation(Context c, LocationListener locationListener)
	{
		LGps gps = new LGps();
		/* 获取到LocationManager对象 */
		locationManager = (LocationManager) c.getSystemService(Context.LOCATION_SERVICE);
		
		if (!locationManager.isProviderEnabled(LocationManager.GPS_PROVIDER)) {
			/* 未开启GPS */
			Log.i("langr", "GPS 未开启");
			writeFile2("/sdcard/org.langr/org.langr.txt", ("\nGPS 未开启").getBytes());
			return null;
			/* 获取大致基站位置？ */
		}
		/* 根据设置的Criteria对象，获取最符合此标准的provider对象 */
		String currentProvider = locationManager.getProvider(LocationManager.GPS_PROVIDER).getName();
		
		/* 根据当前provider对象获取最后一次位置信息 */
		Location currentLocation = locationManager.getLastKnownLocation(currentProvider);
		/* 如果位置信息为null，则请求更新位置信息 */
		if (currentLocation == null) {
			locationManager.requestLocationUpdates(currentProvider, 0, 0, locationListener);
			Log.i("langr", "GPS null");
			writeFile2("/sdcard/org.langr/org.langr.txt", ("\nGPS null").getBytes());
			return null;
			/* 获取大致基站位置？ */
		}
		/* locationManager.removeUpdates(locationListener); */
		/* Location => LGps */
		Log.i("langr", "时间："+currentLocation.getTime());
		Log.i("langr", "经度："+currentLocation.getLongitude());
		Log.i("langr", "纬度："+currentLocation.getLatitude());
		Log.i("langr", "海拔："+currentLocation.getAltitude());
		writeFile2("/sdcard/org.langr/org.langr.txt", ("\nGPS: time:"+currentLocation.getTime()
				+" lat:"+currentLocation.getLatitude()
				+" lng:"+currentLocation.getLongitude()
				+" h:"+currentLocation.getAltitude()).getBytes());
		gps.latitude = currentLocation.getLatitude();
		gps.longitude = currentLocation.getLongitude();
		gps.height = currentLocation.getAltitude();
		gps.time = currentLocation.getTime();
		return gps;
	}

	/* 创建位置监听器 */
	private LocationListener locationListener = new LocationListener() {
		/* 位置发生改变时调用 */
		@SuppressLint("SdCardPath")
		@Override
		public void onLocationChanged(Location currentLocation) {
			Log.d("langr", "Location onLocationChanged");
			writeFile2("/sdcard/org.langr/org.langr.txt", ("\nGPS Changed: time:"+currentLocation.getTime()
				+" lat:"+currentLocation.getLatitude()
				+" lng:"+currentLocation.getLongitude()
				+" h:"+currentLocation.getAltitude()).getBytes());
		}
 
		/* provider失效时调用 */
		@Override
		public void onProviderDisabled(String provider) {
			Log.d("langr", "Location onProviderDisabled");
		}
 
		/* provider启用时调用 */
		@Override
		public void onProviderEnabled(String provider) {
			Log.d("langr", "Location onProviderEnabled");
		}
 
		/* 状态改变时调用 */
		@Override
		public void onStatusChanged(String provider, int status, Bundle extras) {
			Log.d("langr", "Location onStatusChanged");
		}
	};
} 
