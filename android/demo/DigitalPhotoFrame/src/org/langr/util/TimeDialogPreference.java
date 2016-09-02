/** 
 * @file TimeDialogPreference.java
 * @brief 时间首选项设置
 * 
 * Copyright (C) 2014 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package digitalphotoframe
 * @author Langr <hua@langr.org> 2014/11/27 15:01
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: TimeDialogPreference.java 51 2015-01-08 07:33:07Z loghua@gmail.com $
 */

package org.langr.util;

import java.util.Calendar;
import java.util.Date;

import android.content.Context;
import android.content.SharedPreferences;
import android.content.res.TypedArray;
import android.preference.DialogPreference;
import android.preference.PreferenceManager;
import android.util.AttributeSet;
import android.view.View;
import android.widget.TimePicker;
import android.util.Log;
import org.langr.digitalphotoframe.R;

/**
 * select time Preference
 */
public class TimeDialogPreference extends DialogPreference
{
	TimePicker mPicker = null;
	long mValue = 1; 

	public TimeDialogPreference(Context context, AttributeSet attrs) 
	{
		super(context, attrs);
		Log.i("langr", "TimePreference start...");
		/* 加载布局文件 */
		setDialogLayoutResource(R.layout.time_dialog_preference);
		setDialogIcon(R.drawable.btn_star_big_on);
	}

	/**
	 * 绑定此Preference的视图
	 * 弹出此设置对话框的时候进行初始化
	 */
	@Override
	protected void onBindDialogView(View view) 
	{
		Log.i("langr", "TimePreference onBindDialogView...");
		super.onBindDialogView(view);
		
		mPicker = (TimePicker) view.findViewById(R.id.timePicker_preference);
		if (mPicker != null) {
			mPicker.setIs24HourView(true);
			
			//Date d = new Date(mValue);
			Calendar cal = Calendar.getInstance();
			cal.setTimeInMillis(mValue);
			mPicker.setCurrentHour(cal.get(Calendar.HOUR_OF_DAY));
			mPicker.setCurrentMinute(cal.get(Calendar.MINUTE));
		}
	}
	
	/**
	 * 关闭对话框的时候保存
	 */
	@Override
	protected void onDialogClosed(boolean positiveResult) 
	{
		Log.i("langr", "TimePreference onDialogClosed...");
		super.onDialogClosed(positiveResult);
		if (positiveResult) {
			//Date d = new Date(0, 0, 0, mPicker.getCurrentHour(), mPicker.getCurrentMinute(), 0);
			//mValue = d.getTime();
			Calendar cal = Calendar.getInstance();
			cal.set(Calendar.HOUR_OF_DAY, mPicker.getCurrentHour());
			cal.set(Calendar.MINUTE, mPicker.getCurrentMinute());
			mValue = cal.getTimeInMillis();
			
			if (callChangeListener(mValue)) {
				SharedPreferences.Editor vEditor = getEditor();
				vEditor.putLong(getKey(), mValue);//(checkbox_key, false);
				vEditor.commit();
			}
		}
	}
	
	/**
	 * 获取缺省的配置参数
	 */
	@Override
	protected Object onGetDefaultValue(TypedArray a, int index) 
	{
		Log.i("langr", "TimePreference onGetDefaultValue...");
		//String vDatestring = a.getString(index);
		//vDatestring.valueOf(mValue);
		mValue = Long.parseLong(a.getString(index));
		return mValue;
	}

	/**
	 * 获取sharepreference中的配置参数，该函数在配置文件存在时才会被调用
	 */
	@Override
	protected void onSetInitialValue(boolean restorePersistedValue, Object defaultValue) 
	{
		Log.i("langr", "TimePreference onSetInitialValue...");
		long value;
		if (restorePersistedValue) {
			value = getPersistedLong(0);
		} else {
			value = Long.parseLong(defaultValue.toString());
		}
		setDefaultValue(value);
		mValue = value;
	}
}
