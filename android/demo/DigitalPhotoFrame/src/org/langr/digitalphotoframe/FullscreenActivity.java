package org.langr.digitalphotoframe;

import java.io.FileNotFoundException;
import java.util.Calendar;
import java.util.List;

import org.langr.digitalphotoframe.util.SystemUiHider;

import android.annotation.SuppressLint;
import android.annotation.TargetApi;
import android.app.Activity;
import android.preference.PreferenceManager;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.ActivityInfo;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Matrix;
import android.hardware.Sensor;
import android.hardware.SensorEvent;
import android.hardware.SensorEventListener;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
//import android.view.MotionEvent;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;
import android.text.SpannableString;
import android.text.Spanned;
import android.text.StaticLayout;
import android.text.format.DateFormat;
import android.text.style.RelativeSizeSpan;
import android.util.Log;
import org.langr.service.*;
import org.langr.util.SensorLight;
import org.langr.util.CalendarLunar;
import org.langr.util.Files2Json;

/**
 * An example full-screen activity that shows and hides the system UI (i.e.
 * status bar and navigation/system bar) with user interaction.
 *
 * @see SystemUiHider
 */
public class FullscreenActivity extends Activity 
{
	public static String VERSION_STRING = "电子相册 v1.4.20150112\n" +
			"作者：狼狼<hua@langr.org>\n" +
			"http://langr.org";
	private static final String DATE_FORMAT = "yyyy年MM月dd日";
	private static final String TIME_FORMAT = "kk:mm";
	private static final int REQ_SETTINGS = 8;
	private static final int MSG_WHAT_TIMER = 8;
	/* 按两次退出  */
	private static int _c = 0;
	/* 定时执行次数 */
	private int _i = 0;
	/* setting */
	private Boolean mAutoStart = true;
	/* 接收推送 */
	//private Boolean mAllowPush = true;
	/* 开启相册时间(每天) */
	private long mStartTime = 0;
	private Calendar _mStartTime = Calendar.getInstance();
	/* 关闭相册时间(每天) */
	private long mEndTime = 0;
	private Calendar _mEndTime = Calendar.getInstance();
	/* 轮播时间(分钟) */
	private int mPlayTime = 10;
	/* 横屏? */
	private Boolean mHorizontalShow = true;
	/* 显示文本日历? */
	private Boolean mTextCalendarShow = true;
	private CalendarLunar lunar = new CalendarLunar();
	/* 相册路径 */
	private String mPhotosDir = "/";
	/* 当前图片在文件夹中的索引 */
	private int mIdx = 0;
	/* 在相片播放时间结束后显示模拟时钟 */
	private Boolean mAnalogClock = true;
	/* 在光线不足时也显示模拟时钟 */
	private Boolean mNightAnalogClock = true;
	/* 当前相片 */
	//private String mPhoto = null;
	SensorLight mSensorLight = null;

	/**
	 * Whether or not the system UI should be auto-hidden after
	 * {@link #AUTO_HIDE_DELAY_MILLIS} milliseconds.
	 */
	private static final boolean AUTO_HIDE = true;

	/**
	 * If {@link #AUTO_HIDE} is set, the number of milliseconds to wait after
	 * user interaction before hiding the system UI.
	 */
	private static final int AUTO_HIDE_DELAY_MILLIS = 3000;

	/**
	 * If set, will toggle the system UI visibility upon interaction. Otherwise,
	 * will show the system UI visibility upon interaction.
	 */
	private static final boolean TOGGLE_ON_CLICK = true;

	/**
	 * The flags to pass to {@link SystemUiHider#getInstance}.
	 */
	private static final int HIDER_FLAGS = SystemUiHider.FLAG_HIDE_NAVIGATION;

	/**
	 * The instance of the {@link SystemUiHider} for this activity.
	 */
	private SystemUiHider mSystemUiHider;
	/* 相框当前Activity在前台运行？ */
	private boolean mActivityIsForeground = false;
	/* 可以运行定时器线程？在Activity销毁时结束线程！ */
	private boolean mRunTimer = true;
	private TextView mTextView;
	SpannableString msp = null;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		//Log.i("langr", "Fullscreen onCreate start...");
		/* 隐藏标题 */
		//requestWindowFeature(Window.FEATURE_NO_TITLE);
		/* 设置全屏 */
		//getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);
		/* 1. 一直保持屏幕亮起 */
		//getWindow().setFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON, WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
		getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
		setContentView(R.layout.activity_fullscreen);

		final View controlsView = findViewById(R.id.fullscreen_content_controls);
		final ImageView imageView = (ImageView) findViewById(R.id.image_id);
		mTextView = (TextView) findViewById(R.id.digital_calendar);

		// Set up an instance of SystemUiHider to control the system UI for
		// this activity.
		mSystemUiHider = SystemUiHider.getInstance(this, imageView, HIDER_FLAGS);
		mSystemUiHider.setup();
		mSystemUiHider.setOnVisibilityChangeListener(new SystemUiHider.OnVisibilityChangeListener() {
			// Cached values.
			int mControlsHeight;
			int mShortAnimTime;

			@Override
			@TargetApi(Build.VERSION_CODES.HONEYCOMB_MR2)
			public void onVisibilityChange(boolean visible) {
				if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB_MR2) {
					// If the ViewPropertyAnimator API is available
					// (Honeycomb MR2 and later), use it to animate the
					// in-layout UI controls at the bottom of the
					// screen.
					if (mControlsHeight == 0) {
						mControlsHeight = controlsView.getHeight();
					}
					if (mShortAnimTime == 0) {
						mShortAnimTime = getResources().getInteger(
							android.R.integer.config_shortAnimTime);
					}
					controlsView.animate()
						.translationY(visible ? 0 : mControlsHeight)
						.setDuration(mShortAnimTime);
					//Log.i("langr", "onVisibility SystemUiHider..."+visible+" AUTO_HIDE:"+AUTO_HIDE);
				} else {
					// If the ViewPropertyAnimator APIs aren't
					// available, simply show or hide the in-layout UI
					// controls.
					controlsView.setVisibility(visible ? View.VISIBLE : View.GONE);
					//Log.i("langr", "onVisibility SystemUiHider2..."+visible+" AUTO_HIDE:"+AUTO_HIDE+Build.VERSION.SDK_INT);
				}

				if (visible && AUTO_HIDE) {
					// Schedule a hide().
					delayedHide(AUTO_HIDE_DELAY_MILLIS);
				}
			}
		});

		// Set up the user interaction to manually show or hide the system UI.
		imageView.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View view) {
				if (TOGGLE_ON_CLICK) {
					Log.i("langr", "onClick TOGGLE imageView...");
					mSystemUiHider.toggle();
				} else {
					Log.i("langr", "onClick show imageView...");
					mSystemUiHider.show();
				}
			}
		});

		findViewById(R.id.btn_more).setOnClickListener(mOnClickListener);
		findViewById(R.id.btn_perv).setOnClickListener( new View.OnClickListener() {
			@Override
			public void onClick(View view) {
				updatedPhoto(mIdx-1, 0);
			}
		});
		findViewById(R.id.btn_next).setOnClickListener( new View.OnClickListener() {
			@Override
			public void onClick(View view) {
				updatedPhoto(mIdx+1, 0);
			}
		});
		findViewById(R.id.btn_rotate_left).setOnClickListener( new View.OnClickListener() {
			@Override
			public void onClick(View view) {
				updatedPhoto(mIdx, 90);
			}
		});
		findViewById(R.id.btn_rotate_right).setOnClickListener( new View.OnClickListener() {
			@Override
			public void onClick(View view) {
				updatedPhoto(mIdx, -90);
			}
		});
		/* 检测Service是否启动,后台定时检测是否是关闭开启相片显示Activity或切换相片 */
		if (LangrService.isServiceRunning(this, "org.langr.service.LangrService") == false) {
			startService(new Intent(this, LangrService.class));
		}

		mRunTimer = true;
		new Thread(doTimer).start();
		/* 光线 */
		mSensorLight = new SensorLight(this, sensorListen);
	}

	@Override
	protected void onPostCreate(Bundle savedInstanceState) {
		super.onPostCreate(savedInstanceState);

		// Trigger the initial hide() shortly after the activity has been
		// created, to briefly hint to the user that UI controls
		// are available.
		//delayedHide(100);
		mSystemUiHider.hide();
	}

	/**
	 * Touch listener to use for in-layout UI controls to delay hiding the
	 * system UI. This is to prevent the jarring behavior of controls going away
	 * while interacting with activity UI.
	 */
	View.OnClickListener mOnClickListener = new View.OnClickListener() {
		@Override
		public void onClick(View view) {
			Log.i("langr", "onClick bottom !!!设置..."+AUTO_HIDE);
			if (AUTO_HIDE) {
				delayedHide(AUTO_HIDE_DELAY_MILLIS);
			}

			Intent intent = new Intent();
			intent.setClass(FullscreenActivity.this, MoreListActivity.class);
			//startActivity(intent);
			startActivityForResult(intent, REQ_SETTINGS);
			Log.i("langr", "onClick bottom return..."+AUTO_HIDE);

			return;
		}
	};

	Handler mHideHandler = new Handler();
	Runnable mHideRunnable = new Runnable() {
		@Override
		public void run() {
			mSystemUiHider.hide();
		}
	};

	/**
	 * Schedules a call to hide() in [delay] milliseconds, canceling any
	 * previously scheduled calls.
	 */
	private void delayedHide(int delayMillis) {
		mHideHandler.removeCallbacks(mHideRunnable);
		mHideHandler.postDelayed(mHideRunnable, delayMillis);
	}

	/**
	 * 从其他Activity返回
	 */
	protected  void onActivityResult(int requestCode, int resultCode, Intent data)
	{
		/* 设置界面返回 */
		if (requestCode == REQ_SETTINGS) {
			//取得属于整个应用程序的SharedPreferences 
			getSetting();
		} else {
			//其他Intent返回的结果 
		}
		//Toast.makeText(FullscreenActivity.this, "setting return: "+requestCode, Toast.LENGTH_LONG).show(); 
	}
	
	/**
	 * 
	 */
	@Override
	protected void onStart() {
		super.onStart();
		// The activity is about to become visible.
		//Log.i("langr", "Fullscreen onStart...");
	}

	@Override
	protected void onRestart() {
		super.onRestart();
		// The activity is about to become visible.
	}

	@Override
	protected void onResume() {
		super.onResume();
		// The activity has become visible (it is now "resumed").
		Log.i("langr", "Fullscreen onResume...");
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

		/* 在 setContentView 之后，除非把里面的绘图动作取出来 */
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
		Log.i("langr", "Fullscreen onPause...");
		mActivityIsForeground = false;
		/* 2. [取消]一直保持屏幕亮起，当前界面换到后台或退出时 */
		/*
		if (mWakeLock =! null) {
			mWakeLock.release();
		}
		*/
	}

	@Override
	protected void onStop() {
		super.onStop();
		// The activity is no longer visible (it is now "stopped")
		Log.i("langr", "Fullscreen onStop...");
	}

	@Override
	public void finish()
	{
		if ( _c == 1 ) {
			/* 保存 mIdx */
			SharedPreferences settings = PreferenceManager.getDefaultSharedPreferences(this);
			settings.edit().putLong("mIdx", mIdx).commit();
			super.finish();
		} else {
			_c++;
			Log.i("langr", "finish..."+_c+"Toast:"+Toast.LENGTH_LONG);
			Toast.makeText(FullscreenActivity.this, "再按一次退出 ^"+_c, Toast.LENGTH_LONG).show();
			/* 延时一定时间后，清零 _c 计数 */
			mHideHandler.postDelayed(new Runnable() {
				@Override
				public void run() {
					Log.i("langr", "Runnable..."+_c);
					_c = 0;
				}
			}, 3000);
		}
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
	 * getSetting
	 */
	@SuppressLint("SdCardPath")
	protected void getSetting()
	{
		SharedPreferences settings = PreferenceManager.getDefaultSharedPreferences(this);
		mAutoStart = settings.getBoolean("auto_start", true);
		mPhotosDir = settings.getString("photos_dir", "/sdcard/DCIM/Camera");
		mIdx = (int) settings.getLong("mIdx", 0);
		mPlayTime = Integer.parseInt(settings.getString("play_time", "10"));
		mStartTime = settings.getLong("time_start", 0);
		_mStartTime.setTimeInMillis(mStartTime);
		mEndTime = settings.getLong("time_end", 0);
		_mEndTime.setTimeInMillis(mEndTime);
		mHorizontalShow = settings.getBoolean("horizontal_show", true);
		mTextCalendarShow = settings.getBoolean("textcalendar_show", true);
		mAnalogClock = settings.getBoolean("analog_clock", true);
		mNightAnalogClock = settings.getBoolean("night_analog_clock", true);
		//String ring = settings.getString("ring_dida", null);
		//String gender = settings.getString("gender", "哈");

		//Log.v("langr", "preferences return...photos_dir:"+mPhotosDir+",p:"+mIdx);
		//Log.v("langr", "preferences return...play_time "+String.valueOf(mPlayTime));
		//Log.v("langr", "preferences return...time_start "+mStartTime+" endTime:"+mEndTime);
		//Log.v("langr", "preferences return...ring:  "+ring);
		
		updatedPhoto();
		setTextCalendar(Calendar.getInstance());

		return ;
	}

	/**
	 * @brief 切换相片
	 * 	在修改配置和切换时间到时执行此动作;
	 * 	TODO: 取相册文件夹中当前相片的下一张相片并显示。
	 */
	public void updatedPhoto()
	{
		updatedPhoto(mIdx+1, 0);
	}
	
	/**
	 * @brief 
	 * @param idx		索引，-2 取当前默认相片下一张
	 * @param rotate	方向，0,90,-90,180
	 */
	public void updatedPhoto(int idx, int rotate)
	{
		List<String> tmp = FileSelectManager.getDirFiles(mPhotosDir, "image/*");
		if ( tmp == null || tmp.size() < 1 ) {
			Toast.makeText(FullscreenActivity.this, "相框中无图片", Toast.LENGTH_LONG).show();
			return ;
		}
		/* 下一张 */
		if ( idx == -2 ) {
			idx = mIdx + 1;
		}
		/* 到尾/头则从头/尾开始 */
		if ( idx >= tmp.size() ) {
			idx = 0;
		} else if ( idx < 0 ) {
			idx = tmp.size() - 1;
		}
		mIdx = idx;
		String fn = tmp.get(mIdx);
		Log.v("langr", "paths_dir: "+mPhotosDir+" size:"+tmp.size()+" mIdx:"+mIdx);

		/* 取当前图片的旋转记录 */
		Files2Json rotateRec = new Files2Json(mPhotosDir+"/org.langr.dpf.idx");
		int oldRotate = rotateRec.getRotate(fn);

		/* TODO: 动态计算相片需要缩小的比率 */
		BitmapFactory.Options opts = new BitmapFactory.Options();
		opts.inJustDecodeBounds = true;
		//BitmapFactory.decodeStream(this.getContentResolver().openInputStream(Uri.parse(mPhoto)), null, opts);
		BitmapFactory.decodeFile(fn, opts);
		opts.inSampleSize = ClockView.calculateInSampleSize(opts, 1024, 640);
		opts.inJustDecodeBounds = false;
		//opts.inSampleSize = 4;
		try {
			//Bitmap bmp = BitmapFactory.decodeStream(
			//		this.getContentResolver().openInputStream(Uri.parse(mPhoto))
			//		, null, opts);
			Bitmap bmp = BitmapFactory.decodeFile(fn, opts);
			/* 旋转 并 保存 */
			Log.v("langr", "paths: "+fn+" oldRotate:"+oldRotate+" rotate:"+rotate);
			if ( rotate != 0 || oldRotate != 0 ) {
				rotate += oldRotate;
				Matrix matrix = new Matrix();
				matrix.postRotate(rotate);
				bmp = Bitmap.createBitmap(bmp, 0, 0, bmp.getWidth(), bmp.getHeight(), matrix, true);
				/* TODO: save */
				rotateRec.setRotate(fn, rotate);
			}
			ImageView imageView = (ImageView) findViewById(R.id.image_id);
			imageView.setImageBitmap(bmp);
			Log.v("langr", "set image:  "+mIdx+" sample_size:"+opts.inSampleSize);
		//} catch (FileNotFoundException e) {
		//	Log.e("langr", e.getMessage(), e);
		} catch (OutOfMemoryError e) {
			Log.e("langr", "内存不足 sample_size:"+opts.inSampleSize, e);
		}
	}
	
	/**
	 * handler类接收数据
	 * 处理定时线程发送过来的消息  
	 */
	Handler handler = new Handler() {
		public void handleMessage(Message msg) {
			/* (Activity在前台运行时)定时器线程每分钟返回： */
			//Toast.makeText(FullscreenActivity.this, "相框在前台？"+mActivityIsForeground, Toast.LENGTH_LONG).show();
			if (mActivityIsForeground && msg.what == MSG_WHAT_TIMER) {
				/* 需要检测光线改变？ */
				if (mNightAnalogClock) {
					mSensorLight.openLight();
				}
				
				_i++;
				Log.i("langr", "receive message...."+_i);
				Calendar cal = Calendar.getInstance();
				Long now = cal.getTimeInMillis();
				/* 切换相片？ */
				if ((now / 60000) % mPlayTime == 0) {
					Log.i("langr", "换相片时间，前台？"+mActivityIsForeground+":"+cal.get(Calendar.HOUR_OF_DAY)+":"+cal.get(Calendar.MINUTE)+":"+cal.get(Calendar.SECOND)+" photos_dir:"+mPhotosDir);
					//Toast.makeText(FullscreenActivity.this, "换相片时间，在前台？"+mActivityIsForeground, Toast.LENGTH_LONG).show();
					updatedPhoto();
				}
				/**
				 * 相框在前台执行？并且 到退出时间？并且设置了 退出时显示模拟时钟？
				 * (在退出时间段重新打开相框时不再检测是否为退出时间)
				 */
				if (mAnalogClock 
						&& cal.get(Calendar.HOUR_OF_DAY) == _mEndTime.get(Calendar.HOUR_OF_DAY) 
						&& cal.get(Calendar.MINUTE) == _mEndTime.get(Calendar.MINUTE)) {
					/* 启动模拟时钟 */
					Log.i("langr", "intent...org.langr.calendar");
					Toast.makeText(FullscreenActivity.this, "相框时间结束，显示模拟时钟", Toast.LENGTH_LONG).show();
					startAnalogClock();
					//_c = 1;
					//FullscreenActivity.this.finish();
				}
				/* 相片左下角显示日历时间？ */
				setTextCalendar(cal);
			}
		}
	};

	/**
	 * 显示文本日历
	 */
	private void setTextCalendar(Calendar cal)
	{
		if (!mTextCalendarShow) {
			mTextView.setText("@爱老婆的狼狼");
			return ;
		}
		lunar.setCalendar(cal);
		/* length:27~28, 2014年12月15日\n甲午马年\n[闰]十月廿四\n18:01 */
		String textCalendar = DateFormat.format(DATE_FORMAT, cal)+
				"\n"+lunar.cyclical()+lunar.animalsYear()+"年\n"+lunar.getMonth()+lunar.getDay()+"\n"+
				DateFormat.format(TIME_FORMAT, cal);
		msp = new SpannableString(textCalendar);
		//设置字体大小（相对值,单位：像素） 参数表示为默认字体大小的多少倍 
		//msp.setSpan(new RelativeSizeSpan(0.3f), 0, 16, Spanned.SPAN_EXCLUSIVE_EXCLUSIVE);
		msp.setSpan(new RelativeSizeSpan(1.6f), textCalendar.length()-10, textCalendar.length()-6, Spanned.SPAN_EXCLUSIVE_EXCLUSIVE);
		msp.setSpan(new RelativeSizeSpan(3f), textCalendar.length()-5, textCalendar.length(), Spanned.SPAN_EXCLUSIVE_EXCLUSIVE); 
		mTextView.setText(msp);
		//mTextView.setText(textCalendar);
		//Log.i("langr", "text:"+textCalendar.length()+textCalendar);
	}
	
	/**
	 * 模拟时钟
	 */
	public void startAnalogClock()
	{
		/* 启动另一个包的intent */
		//Intent intent = new Intent();
		//intent.setClassName("org.langr.calendar", "org.langr.calendar.ClockActivity");
		//startActivity(intent);
		startActivity(new Intent(FullscreenActivity.this, ClockActivity.class));
	}

	/**
	 * 线程类，处理定时工作
	 * 向相关线程发送一个消息
	 */
	Runnable doTimer = new Runnable () {
		@Override
		public void run() {
			while (mRunTimer) {
				try {
					Thread.sleep(60000);
					Log.i("langr", "timer thread 前台？:"+mActivityIsForeground);
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
					Log.i("langr", "timer thread error...");
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
			Log.i("langr", "Activity:onAccuracyChanged..accuracy:"+accuracy);
		}

		@Override
		public void onSensorChanged(SensorEvent event) {
			// TODO Auto-generated method stub
			Log.i("langr", "Activity:onSensorChanged...mLight:"+event.values[0]+"mV2:"+event.values[1]+"mV3:"+event.values[2]);
			if (event.sensor.getType() == Sensor.TYPE_LIGHT) {
				/* 光线强度值: lux */
				float light = event.values[0];
				/* 一次性获取光线值(lux)，获取后就注销传感器 */
				if (light > 0.1) {
					mSensorLight.closeLight();
				}
				/* 光线变暗？ */
				if (light > 0.1 && light < SensorLight.LIGHT_NIGHT) {
					Toast.makeText(FullscreenActivity.this, "光线变暗，显示模拟时钟"+light, Toast.LENGTH_LONG).show();
					startAnalogClock();
					//_c = 1;
					//FullscreenActivity.this.finish();
				}
			}
		}
	};
}
