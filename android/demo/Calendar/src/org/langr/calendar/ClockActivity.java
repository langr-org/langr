package org.langr.calendar;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.TimeZone;  

import android.app.Activity;
import android.view.Window;
import android.view.WindowManager;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.LinearLayout.LayoutParams;
import android.widget.GridView;
import android.content.BroadcastReceiver;  
import android.content.Context;  
import android.content.Intent;  
import android.content.IntentFilter;  
import android.content.res.Resources;  
import android.graphics.Canvas;  
import android.graphics.drawable.Drawable;  
import android.os.Bundle;
import android.os.Handler;  
import android.os.Message;  
import android.text.format.Time;  
import android.util.AttributeSet;  
import android.util.Log;  
import android.view.View;  
import android.widget.RemoteViews.RemoteView;

/** 
 * AnalogClock View 
 * This widget display an analogic clock with two hands for hours and minutes. 
 */  
@RemoteView  
public class ClockActivity extends Activity {
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_NO_TITLE);
		setContentView(R.layout.clock_view);
	}
}

