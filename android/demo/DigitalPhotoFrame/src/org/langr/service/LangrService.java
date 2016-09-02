/** 
 * @file LangrService.java
 * @brief 服务处理：
 * 	相册定时开启
 * 	在退出相册界面后锁屏前再次启动相册
 * 	自动检测新版本及更新
 * 	推送接收等
 * 
 * Copyright (C) 2015 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package service
 * @author Langr <hua@langr.org> 2015/01/08 15:14
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: LangrService.java 57 2015-01-13 03:58:30Z loghua@gmail.com $
 */

package org.langr.service;

import java.io.File;
import java.io.FileOutputStream;
import java.io.OutputStream;
import java.text.SimpleDateFormat;
import java.util.*;

import android.annotation.SuppressLint;
import android.app.ActivityManager;
import android.app.ActivityManager.RunningServiceInfo;
import android.app.Service;
import android.os.Binder;
import android.os.Bundle;
import android.os.Environment;
import android.os.IBinder;
import android.os.Looper;
import android.preference.PreferenceFragment;
import android.content.Context;
import android.content.Intent;
import android.location.Location;
//import android.view.MotionEvent;
import android.net.Uri;
import android.util.Log;
import org.langr.util.*;

import android.location.LocationListener;
import android.location.LocationManager;

import com.amap.api.location.AMapLocation;
import com.amap.api.location.AMapLocationListener;
import com.amap.api.location.LocationManagerProxy;
import com.amap.api.location.LocationProviderProxy;

public class LangrService extends Service 
{
	/* 当前连接数 */
	private int binds = 0;
	private Long start_time;
	private LrBinder mBinder = new LrBinder();
	private boolean quit = false;
	/* amap Cell Location */
	private LocationManagerProxy mLocationManagerProxy;
	private AMapLocationListener mAMapLocationListener = new AMapLocationListener() {
		Gps2Json gps = new Gps2Json("/sdcard/org.langr/org.langr.gps.json");
		
		@Override
		public void onLocationChanged(Location arg0) {
			// TODO Auto-generated method stub
		}

		@Override
		public void onProviderDisabled(String arg0) {
			// TODO Auto-generated method stub
		}

		@Override
		public void onProviderEnabled(String arg0) {
			// TODO Auto-generated method stub
		}

		@Override
		public void onStatusChanged(String arg0, int arg1, Bundle arg2) {
			// TODO Auto-generated method stub
		}

		@SuppressLint("SdCardPath")
		@Override
		public void onLocationChanged(AMapLocation g) {
			if (g != null
					&& g.getAMapException().getErrorCode() == 0) {
				/* 定位成功回调信息，设置相关消息 */
				SimpleDateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
				Date date = new Date(g.getTime());
				String info = "amap:"+df.format(date)+" "+g.getLatitude()+","+g.getLongitude()
					+ ",h:" + String.valueOf(g.getAccuracy()) + ",p" + g.getProvider()
					+ ",a:" + g.getAddress() 
					+ ",c:" + g.getCountry() + g.getProvince()
					+ ",city:" + g.getCity()
					+ ",d:" + g.getDistrict() + g.getRoad()
					+ ",poi:" + g.getPoiName()
					+ ",code:" + g.getCityCode() + ",adcode" + g.getAdCode();
				Log.i("langr", info);
				writeFile2("/sdcard/org.langr/org.langr.txt", info.getBytes(), true);
				Calendar cal = Calendar.getInstance();
				String fn = "/sdcard/org.langr/json" + cal.get(Calendar.YEAR) + cal.get(Calendar.MONTH);
				gps.appendGps(g.getLongitude(), g.getLatitude(), g.getAccuracy(), fn);
			} else {
				Log.e("langr","AMap ERR:" + g.getAMapException().getErrorMessage());
				writeFile2("/sdcard/org.langr/org.langr.txt", ("AMap Err:"+ g.getAMapException().getErrorMessage()).getBytes(), true);
			}
		}
	};

	/* 创建位置监听器 */
	private LocationListener mLocationListener = new LocationListener() {
		/* 位置发生改变时调用 */
		@SuppressLint("SdCardPath")
		@Override
		public void onLocationChanged(Location currentLocation) {
			Log.d("langr", "Service Location onLocationChanged");
			writeFile2("/sdcard/org.langr/org.langr.txt", ("\nService GPS Changed: time:"+currentLocation.getTime()
				+" lat:"+currentLocation.getLatitude()
				+" lng:"+currentLocation.getLongitude()
				+" h:"+currentLocation.getAltitude()).getBytes(), true);
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

	/* 与客户端通信接口 */
	public class LrBinder extends Binder
	{
		public int getBinds()
		{
			Log.i("langr", "service getBinds ...");
			return binds;
		}
		
		public boolean doQuit()
		{
			Log.i("langr", "service doQuit...");
			quit = true;
			return quit;
		}
	}

	@SuppressLint("SdCardPath")
	@Override
	public void onCreate()
	{
		super.onCreate();
		Log.i("langr", "service onCreate start...");
		//File sdDir = new File(Environment.getExternalStorageDirectory()+"/org.langr");
		File sdDir = new File("/sdcard/org.langr");
		if (!sdDir.exists()) {
			sdDir.mkdirs();
		}
		/* 初始化定位，只采用网络定位 */
		mLocationManagerProxy = LocationManagerProxy.getInstance(this);
		//mLocationManagerProxy.setGpsEnable(false);
		/** 
		 * 此方法为每隔固定时间会发起一次定位请求，为了减少电量消耗或网络流量消耗，
		 * 注意设置合适的定位时间的间隔（最小间隔支持为2000ms），并且在合适时间调用removeUpdates()方法来取消定位请求
		 * 在定位结束后，在合适的生命周期调用destroy()方法
		 * 其中如果间隔时间为-1，则定位只定一次,
		 * 在单次定位情况下，定位无论成功与否，都无需调用removeUpdates()方法移除请求，定位sdk内部会移除
		 */
		mLocationManagerProxy.requestLocationData(LocationProviderProxy.AMapNetwork, 120 * 1000, 15, mAMapLocationListener);
		new Thread() {
			@SuppressLint("SdCardPath") 
			@Override
			public void run()
			{
				Gps2Json gps = new Gps2Json("/sdcard/org.langr/org.langr.gps.json");
				Gps2Json.LGps g =  null;
				String bfn = "/sdcard/org.langr/json";
				Looper.prepare();
				while (!quit) {
					Calendar cal = Calendar.getInstance();
					String fn = bfn + cal.get(Calendar.YEAR) + cal.get(Calendar.MONTH);
					try {
						Log.i("langr", "service beat..."+binds);
						Thread.sleep(120000);
					} catch (InterruptedException e) {
						Log.e("langr", "service Thread error...");
					}

					g = gps.getCellLocation(LangrService.this);
					if (g != null) {
						gps.appendGps(g.longitude, g.latitude, g.height, fn);
						Log.i("langr", "Cell: lng"+g.longitude+" lat:"+g.latitude+"height:"+g.height);
					}
					/* 在线程中使用位置服务需要消息循环 */
					g = gps.getGpsLocation(LangrService.this, mLocationListener);
					if (g != null) {
						gps.appendGps(g.longitude, g.latitude, g.height, fn);
						Log.i("langr", "Gps: lng"+g.longitude+" lat:"+g.latitude+"height:"+g.height);
					}
				}
				Looper.loop();
			}
		}.start();
	}

	@Override
	public int onStartCommand(Intent intent, int flags, int startId)
	{
		//super.onStartCommand(intent, flags, startId);
		Log.i("langr", "service onStartCommand start..."+startId+intent.toString());
		return START_STICKY;
	}

	/**
	 * 关闭服务时调用
	 */
	@Override
	public void onDestroy() {
		super.onDestroy();
		this.quit = true;
		/* 移除定位请求 */
		mLocationManagerProxy.removeUpdates(mAMapLocationListener);
		/* 销毁定位 */
		mLocationManagerProxy.destroy();
		Log.i("langr", "service onDestroy...");
	}

	/**
	 * 
	 */
	@Override
	public IBinder onBind(Intent intent) 
	{
		Log.i("langr", "service onBind...");
		binds++;
		return mBinder;
	}
	
	/**
	 * 
	 */
	public boolean onUnBind(Intent intent) 
	{
		Log.i("langr", "service onUnBind...");
		binds--;
		return true;
	}
	
	/**
	 * 检测指定服务是否已经启动
	 * @param Context context
	 * @param String serviceName
	 * @return true
	 */
	public static boolean isServiceRunning(Context context, String serviceName) {
		ActivityManager manager = (ActivityManager) context.getSystemService(Context.ACTIVITY_SERVICE);
		for (RunningServiceInfo s : manager.getRunningServices(Integer.MAX_VALUE)) {
			if (serviceName.equals(s.service.getClassName())) {
				return true;
			}
		}
		return false;
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
}
