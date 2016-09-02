package org.langr.digitalphotoframe;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.TimeZone;  

import org.langr.util.SensorLight;

import android.app.Activity;
import android.view.Window;
import android.view.WindowManager;
//import android.widget.TextClock;
import android.content.Context;
import android.content.Intent;  
import android.content.SharedPreferences;
import android.content.pm.ActivityInfo;
import android.hardware.Sensor;
import android.hardware.SensorEvent;
import android.hardware.SensorEventListener;
import android.os.Bundle;
import android.os.Handler;  
import android.os.Message;
import android.preference.PreferenceManager;
import android.util.Log;

/** 
 * AnalogClock View 
 * This widget display an analogic clock with two hands for hours and minutes. 
 */  

public class ClockActivity extends Activity {
	/* setting */
	private Boolean mAutoStart = true;
	/* 开启相册时间(每天) */
	private long mStartTime = 0;
	private Calendar _mStartTime = Calendar.getInstance();
	/* 关闭相册时间(每天) */
	private long mEndTime = 0;
	private Calendar _mEndTime = Calendar.getInstance();
	/* 横屏? */
	private Boolean mHorizontalShow = true;
	/* 在相片播放时间结束后显示模拟时钟 */
	private Boolean mAnalogClock = true;
	/* 在光线不足时也显示模拟时钟 */
	private Boolean mNightAnalogClock = true;
	/* 模拟时钟滴答声 */
	private String mDida = null;
	/* 光线 */
	SensorLight mSensorLight = null;
	/* 模拟时钟在前台运行？ */
	private boolean mActivityIsForeground = false;
	/* 可以运行定时器线程？在Activity销毁时结束线程！ */
	private boolean mRunTimer = true;
	private static final int MSG_WHAT_TIMER = 8;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_NO_TITLE);
		//getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);
		getWindow().addFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN);
		getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
		setContentView(R.layout.clock_view);
		
		//TextClock textClock = (TextClock) findViewById(R.id.digital_clock);
		//textClock.setFormat24Hour("k:mm");

		Log.i("langr", "Analog Clock start...");
		mRunTimer = true;
		new Thread(doTimer).start();

		/* 准备光线传感器 */
		mSensorLight = new SensorLight(this, sensorListen);
	}

	@Override
	protected void onResume() {
		super.onResume();
		// The activity has become visible (it is now "resumed").
		//Log.i("langr", "onResume...");
		/**
		 * 2. 一直保持屏幕亮起
		 * 权限：
		 * <uses-permission android:name="android.permission.WAKE_LOCK" />
		 * <uses-permission android:name="android.permission.DEVICE_POWER" />
		 * PowerManager pManager;
		 * WakeLock mWakeLock;
		 */
		/*
		pManager = ((PowerManager) getSystemService(POWER_SERVICE));
		mWakeLock = pManager.newWakeLock(PowerManager.SCREEN_BRIGHT_WAKE_LOCK | PowerManager.ON_AFTER_RELEASE, TAG);
		mWakeLock.acquire();
		*/

		/* 在 setContentView 之后 */
		getSetting();
		mActivityIsForeground = true;

		/* 横屏？ */
		if (mHorizontalShow && getRequestedOrientation() != ActivityInfo.SCREEN_ORIENTATION_LANDSCAPE) {
			setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_LANDSCAPE);
		} else if (!mHorizontalShow && getRequestedOrientation() != ActivityInfo.SCREEN_ORIENTATION_PORTRAIT) {
			setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
		}
	}
	
	@Override
	protected void onPause() {
		super.onPause();
		// Another activity is taking focus (this activity is about to be "paused").
		Log.i("langr", "Clock onPause...");
		mActivityIsForeground = false;
		/* 2. [取消]一直保持屏幕亮起，当前界面换到后台或退出时 */
		/*
		if (mWakeLock =! null) {
			mWakeLock.release();
		}
		*/
	}
	
	@Override
	protected void onDestroy() {
		super.onDestroy();
		// The activity is about to be destroyed.
		Log.i("langr", "Fullscreen onDestroy...");
		/* 结束定时线程 */
		mRunTimer = false;
	}
	
	/**
	 * 启动相册
	 */
	public void startAlbum()
	{
		/* 启动另一个包的intent */
		//Intent intent = new Intent();
		//intent.setClassName("org.langr.digitalphotoframe", "org.langr.digitalphotoframe.FullscreenActivity");
		//startActivity(intent);
		/* 启动新的Intent */
		//startActivity(new Intent(ClockActivity.this, FullscreenActivity.class));
		/* 返回被调动的Activity...傻了，直接退出就返回了 */
		//getIntent();
		finish();
	}

	/**
	 * getSetting
	 */
	protected void getSetting()
	{
		SharedPreferences settings = PreferenceManager.getDefaultSharedPreferences(this);
		mAutoStart = settings.getBoolean("auto_start", true);
		mStartTime = settings.getLong("time_start", 10);
		_mStartTime.setTimeInMillis(mStartTime);
		mEndTime = settings.getLong("time_end", 10);
		_mEndTime.setTimeInMillis(mEndTime);
		mHorizontalShow = settings.getBoolean("horizontal_show", true);
		mAnalogClock = settings.getBoolean("analog_clock", true);
		mNightAnalogClock = settings.getBoolean("night_analog_clock", true);
		mDida = settings.getString("ring_dida", "ring");

		Log.v("langr", "preferences return...time_start "+mStartTime+" endTime:"+mEndTime);
		Log.v("langr", "preferences return...auto_start "+mHorizontalShow.toString());
		Log.v("langr", "preferences return...ring:  "+mDida);
		
		return ;
	}

	/**
	 * handler类接收数据
	 * 处理定时线程发送过来的消息  
	 */
	Handler handler = new Handler() {
		public void handleMessage(Message msg) {
			/* 定时器线程返回： */
			if (mActivityIsForeground && msg.what == MSG_WHAT_TIMER) {
				Calendar cal = Calendar.getInstance();
				Long now = cal.getTimeInMillis();
				/** 
				 * 到相册启动时间？启动相册：
				 * 打开光线感应器监听，监听到光线充足时打开相册，并关闭光线监听
				 * 启动相册的条件(需同时满足)：
				 * 1.在相册设置的启动时间 
				 * 2.光线充足
				 */
				if (timeBucket(cal.get(Calendar.HOUR_OF_DAY)*60+cal.get(Calendar.MINUTE), 
						_mStartTime.get(Calendar.HOUR_OF_DAY)*60+_mStartTime.get(Calendar.MINUTE), 
						_mEndTime.get(Calendar.HOUR_OF_DAY)*60+_mEndTime.get(Calendar.MINUTE))) {
				//if ( cal.get(Calendar.HOUR_OF_DAY) == _mStartTime.get(Calendar.HOUR_OF_DAY) 
				//		&& cal.get(Calendar.MINUTE) == _mStartTime.get(Calendar.MINUTE) ) {
					mSensorLight.openLight();
					Log.i("langr", "Clock...光线允许则可以启动相册"+mActivityIsForeground);
					//startAlbum();
				}
			}
		}
	};

	/**
	 * 判断给定时间是否是一天中指定的启止时间段
	 * @param int current	当前要判断的时间，当天的第current分钟
	 * @param int s			当天时间段的开始时间，即第s分钟开始
	 * @param int e			当天时间段的结束时间，即第e分钟结束
	 * @return true 在开始时间段，false 在结束时间段
	 */
	public static boolean timeBucket(int current, int s, int e)
	{
		//int day = 1440;		/* 一天的总分钟数 */
		/* 结束时间在0点之前，在每天的开始时间之后 */
		if (e > s) {
			/* 结束时间段 */
			if (current < s || current >= e) {
				return false;
			/* 开始时间段 */
			} else if (current >= s && current < e) {
				return true;
			}
		/* 结束时间在0点之后，在每天的开始时间之前 */
		} else if (e < s) {
			/* 结束时间段 */
			if (current < s && current >= e) {
				return false;
			/* 开始时间段 */
			} else if (current >= s || current < e) {
				return true;
			}
		}
		return true;
	}
	
	/**
	 * 线程类，处理定时工作
	 * 向相关线程发送一个消息
	 */
	Runnable doTimer = new Runnable () {
		@Override
		public void run() {
			// TODO Auto-generated method stub
			while (mRunTimer) {
				try {
					Thread.sleep(60000);
					Log.i("langr", "Clock timer thread 前台？:"+mActivityIsForeground);
					/* 在前台时才发送消息 */
					if (!mActivityIsForeground) {
						continue;
					}
					/* 这里处理定时相关事情，或者传消息到相关线程处理 */
					//Message.obtain(handler, MSG_WHAT_TIMER);
					Message msg = new Message();
					msg.what = MSG_WHAT_TIMER;
					handler.sendMessage(msg);
					/* TODO: ... */
				} catch (Exception e) {
					Log.i("langr", "Clock timer thread error...");
				}
			}
		}
	};

	/* 光线变化监听 */
	SensorEventListener sensorListen = new SensorEventListener() {
		@Override
		public void onAccuracyChanged(Sensor sensor, int accuracy) {
			// TODO Auto-generated method stub
			if (sensor.getType() == Sensor.TYPE_LIGHT) {
				//设置将accuracy的值显示到屏幕上
				//mAccuracy = accuracy;
			}
			Log.i("langr", "AnalogClock:onAccuracyChanged...accuracy:"+accuracy);
		}

		@Override
		public void onSensorChanged(SensorEvent event) {
			// TODO Auto-generated method stub
			Log.i("langr", "Analogclock:onSensorChanged...mLight:"+event.values[0]+"mV2:"+event.values[1]+"mV3:"+event.values[2]);
			if (event.sensor.getType() == Sensor.TYPE_LIGHT) {
				/* 光线强度值: lux */
				float light = event.values[0];
				/* 一次性获取光线值(lux)，获取后就注销传感器 */
				if (light > 0.1) {
					mSensorLight.closeLight();
				}
				/* 光线变亮？ */
				if (light > SensorLight.LIGHT_NIGHT) {
					Log.i("langr", "Clock intent...启动相册"+mActivityIsForeground);
					startAlbum();
					//ClockActivity.this.finish();
				}
			}
		}
	};
}

