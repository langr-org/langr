package org.langr.digitalphotoframe;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.TimeZone;

import android.annotation.SuppressLint;
import android.content.Context;
import android.content.res.Resources;
import android.view.Display;
import android.view.Gravity;
import android.view.WindowManager;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.LinearLayout.LayoutParams;
import android.widget.GridView;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.res.Resources;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Canvas;
import android.graphics.Matrix;
import android.graphics.Paint;
import android.graphics.PixelFormat;
import android.graphics.PorterDuff.Mode;
import android.graphics.PorterDuffXfermode;
import android.graphics.drawable.BitmapDrawable;
import android.graphics.drawable.Drawable;
import android.os.Handler;
import android.os.Message;
import android.text.format.Time;
import android.util.AttributeSet;
import android.util.Log;
import android.view.View;
import android.widget.RemoteViews.RemoteView;

/** 
 * AnalogClockView 
 * This widget display an analogic clock with two hands for hours and minutes. 
 */
public class ClockView extends View {

	private Time mCalendar;
	private Drawable mHourHand;
	private Drawable mMinuteHand;
	private Drawable mSecondHand;
	private Drawable mDial;

	private int mDialWidth;
	private int mDialHeight;

	private boolean mAttached;

	private float mMinutes;
	private float mHour;

	private boolean mChanged;

	// 增加了秒针显示所用到的秒表
	private static String debug = "ClockView";

	private final static int SECONDS_FLAG = 0;
	private Message secondsMsg;
	private float mSeconds;
	// end

	private final Handler mHandler = new Handler() {
		@Override
		public void handleMessage(Message msg) {
			switch (msg.what) {
			case SECONDS_FLAG:
				onTimeChanged();// 重新获取的系统的当前时间，得到时，分，秒
				invalidate();// 强制绘制，调用自身的onDraw();
				break;

			default:
				break;
			}
			super.handleMessage(msg);
		}
	};

	public ClockView(Context context) {
		this(context, null);
	}

	public ClockView(Context context, AttributeSet attrs) {
		this(context, attrs, 0);
	}

	public ClockView(Context context, AttributeSet attrs, int defStyle) {
		super(context, attrs, defStyle);
		Resources r = context.getResources();

		if (mDial == null) {
			mDial = r.getDrawable(R.drawable.clock_dial);
		}
		if (mHourHand == null) {
			mHourHand = r.getDrawable(R.drawable.clock_hand_hour);
		}
		if (mMinuteHand == null) {
			mMinuteHand = r.getDrawable(R.drawable.clock_hand_minute);
		}
		if (mSecondHand == null) {
			mSecondHand = r.getDrawable(R.drawable.clock_hand_second);
		}
		
		mCalendar = new Time();

		mDialWidth = mDial.getIntrinsicWidth();
		mDialHeight = mDial.getIntrinsicHeight();
	}

	/* 
	 * * 吸附到窗体上 
	 */
	@Override
	protected void onAttachedToWindow() {
		Log.i(debug, "onAttachedToWindow");
		super.onAttachedToWindow();

		if (!mAttached) {
			mAttached = true;
			// /////////修改到秒针显示后，不需要广播接收器
			// IntentFilter filter = new IntentFilter();
			//
			// filter.addAction(Intent.ACTION_TIME_TICK);// 每隔一分钟会发出这样的一个action
			// filter.addAction(Intent.ACTION_TIME_CHANGED);//
			// 外部修改系统时间，发出这样的action
			// filter.addAction(Intent.ACTION_TIMEZONE_CHANGED);//
			// 外部修改系统的时区，发出action

			// getContext().registerReceiver(mIntentReceiver, filter);
			// null,handler 这两个参数暂时无效，故去掉，
			// ///////end
		}

		// NOTE: It's safe to do these after registering the receiver since the
		// receiver always runs
		// in the main thread, therefore the receiver can't run before this
		// method returns.

		// The time zone may have changed while the receiver wasn't registered,
		// so update the Time
		mCalendar = new Time();

		// Make sure we update to the current time
		onTimeChanged();

		initSecondsThread();
	}

	private void initSecondsThread() {
		secondsMsg = mHandler.obtainMessage(SECONDS_FLAG);
		Thread newThread = new Thread() {
			@Override
			public void run() {
				while (mAttached) {
					// 如果这个消息不重新获取的话，
					// 会抛一个this message is already in use 的异常
					secondsMsg = mHandler.obtainMessage(SECONDS_FLAG);
					// /end
					mHandler.sendMessage(secondsMsg);
					try {
						sleep(1000);
					} catch (InterruptedException e) {
						e.printStackTrace();
					}
				}

			}
		};
		newThread.start();
	}

	/** 
	 * 脱离窗体 在按home按键，不触发这个事件，所以这个应用的监听还是持续监听着。 
	 * 如果外部修改系统事件，action=Intent.ACTION_TIME_CHANGED 按back按键，触发事件，下次从onCreate从新载入
	 */
	@Override
	protected void onDetachedFromWindow() {
		Log.i(debug, "onDetachedFromWindow");
		super.onDetachedFromWindow();
		if (mAttached) {
			// getContext().unregisterReceiver(mIntentReceiver); 增加秒针，不需要接收器了。
			mAttached = false;
		}
	}

	@Override
	protected void onMeasure(int widthMeasureSpec, int heightMeasureSpec) {
		Log.i(debug, "onMeasure");
		int widthMode = MeasureSpec.getMode(widthMeasureSpec);
		int widthSize = MeasureSpec.getSize(widthMeasureSpec);
		int heightMode = MeasureSpec.getMode(heightMeasureSpec);
		int heightSize = MeasureSpec.getSize(heightMeasureSpec);

		float hScale = 1.0f;
		float vScale = 1.0f;

		if (widthMode != MeasureSpec.UNSPECIFIED && widthSize < mDialWidth) {
			hScale = (float) widthSize / (float) mDialWidth;
		}

		if (heightMode != MeasureSpec.UNSPECIFIED && heightSize < mDialHeight) {
			vScale = (float) heightSize / (float) mDialHeight;
		}

		float scale = Math.min(hScale, vScale);

		setMeasuredDimension(resolveSize((int) (mDialWidth * scale),
				widthMeasureSpec), resolveSize((int) (mDialHeight * scale),
				heightMeasureSpec));
	}

	@Override
	protected void onSizeChanged(int w, int h, int oldw, int oldh) {
		Log.i("langr", "onSizeChanged:oldw"+oldw+"oldh:"+oldh+"w:"+w+"h:"+h);
		super.onSizeChanged(w, h, oldw, oldh);
		/* TODO: 拉大钟布，拉长各指针 */
		//mDial = zoomDrawable(mDial, w, h);
		//mHourHand = zoomDrawable(mHourHand, 0, h>>1);
		//mMinuteHand = zoomDrawable(mMinuteHand, 0, h>>1);
		//mSecondHand = zoomDrawable(mSecondHand, 0, h>>1);
		mChanged = true;
	}

	@Override
	@SuppressLint("DrawAllocation")
	protected void onDraw(Canvas canvas) {
		super.onDraw(canvas);

		/* 清除画布，避免部分机器出现重影 */
		Paint mPaint = new Paint();
		mPaint.setAntiAlias(true);//抗锯齿
		// canvas 清除画布内容。
		mPaint.setXfermode(new PorterDuffXfermode(Mode.CLEAR));
        canvas.drawPaint(mPaint);
        
		boolean changed = mChanged;
		if (changed) {
			mChanged = false;
		}

		int availableWidth = getWidth();
		int availableHeight = getHeight();

		int x = availableWidth / 2;
		int y = availableHeight / 2;

		final Drawable dial = mDial;
		int w = dial.getIntrinsicWidth();
		int h = dial.getIntrinsicHeight();

		boolean scaled = false;

		/**
		 * 当画布比时钟面板小时，(在[x,y]中心位置)缩放画布比例到与时钟面板适合
		 * @modify 让时钟面板一直适合画布
		 */
		//if (availableWidth < w || availableHeight < h) {
			scaled = true;
			float scale = Math.min((float) availableWidth / (float) w,
					(float) availableHeight / (float) h);
			canvas.save();
			canvas.scale(scale, scale, x, y);
		//}

		/* 窗口大小改变时，设置时钟面板边界 */
		if (changed) {
			dial.setBounds(x - (w / 2), y - (h / 2), x + (w / 2), y + (h / 2));
		}
		dial.draw(canvas);
		// 以上是绘制12个小时背景图

		canvas.save();
		/* 旋转画布方向到当前的时针位置，再画时针 */
		canvas.rotate(mHour / 12.0f * 360.0f, x, y);

		/* 窗口大小改变时，设置时针边界 */
		final Drawable hourHand = mHourHand;
		if (changed) {
			w = hourHand.getIntrinsicWidth();
			h = hourHand.getIntrinsicHeight();
			hourHand.setBounds(x - (w / 2), y - (h / 2), x + (w / 2), y
					+ (h / 2));
		}
		/* 将时针画到画布上，并恢复(上一次)画布的旋转 */
		hourHand.draw(canvas);
		canvas.restore();
		// 以上是绘制时针

		canvas.save();
		canvas.rotate(mMinutes / 60.0f * 360.0f, x, y);

		final Drawable minuteHand = mMinuteHand;
		if (changed) {
			w = minuteHand.getIntrinsicWidth();
			h = minuteHand.getIntrinsicHeight();
			minuteHand.setBounds(x - (w / 2), y - (h / 2), x + (w / 2), y
					+ (h / 2));
		}
		minuteHand.draw(canvas);
		canvas.restore();
		// 以上是绘制分针

		// 增加秒针的绘制
		canvas.save();
		canvas.rotate(mSeconds / 60.0f * 360.0f, x, y);

		final Drawable secondHand = mSecondHand;	// 秒针
		if (changed) {
			w = secondHand.getIntrinsicWidth();
			h = secondHand.getIntrinsicHeight();
			secondHand.setBounds(x - (w / 2), y - (h / 2), x + (w / 2), y
					+ (h / 2));
		}
		secondHand.draw(canvas);
		canvas.restore();
		// end

		/* 恢复(上一次)画布的缩放 */
		if (scaled) {
			canvas.restore();
		}
	}

	/** 
	 * 改变时间 
	 */
	private void onTimeChanged() {
		mCalendar.setToNow();// ///获取手机自身的当前时间，而非实际中的标准的北京时间

		int hour = mCalendar.hour;// 小时
		int minute = mCalendar.minute;// 分钟
		int second = mCalendar.second;// 秒

		mSeconds = second;
		mMinutes = minute + second / 60.0f;
		mHour = hour + mMinutes / 60.0f;

		mChanged = true;
	}

	/** 
	 * 这个接收器，只接受三个action， 1.Intent.ACTION_TIME_TICK，每分钟发出一次 
	 * 2.Intent.ACTION_TIME_CHANGE， 外部修改系统时间 3.Intent.ACTION_TIMEZONE_CHANGED 
	 * 外部系统的时区 按home，还能继续监听 按back，监听销毁 
	 */
	private final BroadcastReceiver mIntentReceiver = new BroadcastReceiver() {
		@Override
		public void onReceive(Context context, Intent intent) {
			Log.i(debug, "intent action=" + intent.getAction());
			if (intent.getAction().equals(Intent.ACTION_TIMEZONE_CHANGED)) {
				String tz = intent.getStringExtra("time-zone");
				mCalendar = new Time(TimeZone.getTimeZone(tz).getID());
			}
			onTimeChanged();
			invalidate();
		}
	};

	/**
	 * drawable 转换成 bitmap
	 */
	static Bitmap drawableToBitmap(Drawable drawable)
	{
		int width = drawable.getIntrinsicWidth();	// 取 drawable 的长宽
		int height = drawable.getIntrinsicHeight();
		// 取 drawable 的颜色格式
		Bitmap.Config config = drawable.getOpacity() != PixelFormat.OPAQUE ? Bitmap.Config.ARGB_8888:Bitmap.Config.RGB_565;
		Bitmap bitmap = Bitmap.createBitmap(width, height, config);	// 建立对应 bitmap
		Canvas canvas = new Canvas(bitmap);		// 建立对应 bitmap 的画布
		drawable.setBounds(0, 0, width, height);
		drawable.draw(canvas);				// 把 drawable 内容画到画布中
		return bitmap;
	}

	/**
	 * 缩放drawable
	 * @param w	目标宽，0表示不改变宽度
	 * @param h	目标高，0表示不改变高度
	 */
	@SuppressWarnings("deprecation")
	static Drawable zoomDrawable(Drawable drawable, int w, int h)
	{
		int width = drawable.getIntrinsicWidth();
		int height= drawable.getIntrinsicHeight();
		Bitmap oldbmp = drawableToBitmap(drawable);	// drawable 转换成 bitmap
		Matrix matrix = new Matrix();			// 创建操作图片用的 Matrix 对象
		float scaleWidth = 1;
		float scaleHeight = 1;
		if ( w > 0 ) {
			scaleWidth = ((float)w / width);		// 计算缩放比例
		}
		if ( h > 0 ) {
			scaleHeight = ((float)h / height);
		}
		matrix.postScale(scaleWidth, scaleHeight);	// 设置缩放比例
		// 建立新的 bitmap ，其内容是对原 bitmap 的缩放后的图
		Bitmap newbmp = Bitmap.createBitmap(oldbmp, 0, 0, width, height, matrix, true);
		//oldbmp = Bitmap.createScaledBitmap(oldbmp, w, h, false);
		return new BitmapDrawable(newbmp);		// 把 bitmap 转换成 drawable 并返回
	}
	
	/**
	 * 通过将宽与高进行比对得出的inSampleSize值。
	 * Calculate an inSampleSize for use in a {@link BitmapFactory.Options} 
	 * object when decoding bitmaps using the decode* methods from 
	 * {@link BitmapFactory}. This implementation calculates the closest 
	 * inSampleSize that will result in the final decoded bitmap having a width 
	 * and height equal to or larger than the requested width and height. This 
	 * implementation does not ensure a power of 2 is returned for inSampleSize 
	 * which can be faster when decoding but results in a larger bitmap which 
	 * isn't as useful for caching purposes. 
	 * @param options 
	 *            An options object with out* params already populated (run 
	 *            through a decode* method with inJustDecodeBounds==true 
	 * @param reqWidth 
	 *            The requested width of the resulting bitmap 
	 * @param reqHeight 
	 *            The requested height of the resulting bitmap 
	 * @return The value to be used for inSampleSize 
	 */
	public static int calculateInSampleSize(BitmapFactory.Options options, int reqWidth, int reqHeight) 
	{
		// Raw height and width of image
		final int height = options.outHeight;
		final int width = options.outWidth;
		int inSampleSize = 1;

		//先根据宽度进行缩小
		while (width / inSampleSize > reqWidth) {
			inSampleSize++;
		}
		//然后根据高度进行缩小
		while (height / inSampleSize > reqHeight) {
			inSampleSize++;
		}
		return inSampleSize;
	}
}

