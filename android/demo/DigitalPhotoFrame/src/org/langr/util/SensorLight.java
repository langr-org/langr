/** 
 * @file SensorLight.java
 * @brief 
 * 
 * Copyright (C) 2014 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package util
 * @author Langr <hua@langr.org> 2014/12/10 18:14
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: SensorLight.java 54 2015-01-09 09:02:32Z loghua@gmail.com $
 */

package org.langr.util;

import java.util.Date;

import android.content.Context;
import android.hardware.Sensor;
import android.hardware.SensorEvent;
import android.hardware.SensorEventListener;
import android.hardware.SensorManager;
import android.util.Log;

/**
 * 获取光线传感器光线值
 */
public class SensorLight 
{
	/* 夜晚光线值 */
	public static final float LIGHT_NIGHT = (float) 20.0;
	/* 传感器管理器引用 */
	SensorManager sensorManager = null;
	/* 光线传感器引用 */
	Sensor lightSensor = null;
	/* 光线值: lux */
	/**
	 * LIGHT_NO_MOON:晚上没月亮时的lux值==0.001
	 * LIGHT_FULLMOON:晚上满月时的lux值==0.25
	 * LIGHT_CLOUDY:多云天气下的lux值==100.0
	 * LIGHT_SUNRISE:黎明时的lux值==400.0
	 * LIGHT_OVERCAST:阴天的lux值==10000.0
	 * LIGHT_SHADE:阴影下的lux值==20000.0
	 * LIGHT_SUNLIGHT:阳光下的lux值==110000.0
	 * LIGHT_SUNLIGHT_MAX:阳光下的最大lux值==120000.0
	 */
	float mLight = 0;
	/* 精确度 */
	int mAccuracy = 0;
	//SensorEventListener sensorListen = null;
	Context context = null;

	public SensorLight(Context c)
	{
		this(c, null);
		Log.i("langr", "SensorLight1..."+context.toString());
	}
	
	public SensorLight(Context c, SensorEventListener eventListener)
	{
		context = c;
		if (eventListener != null) {
			sensorListen = eventListener;
		}
		Log.i("langr", "SensorLight2..."+context.toString());
		//openLight();
	}
	
	public void openLight()
	{
		/* 传感器管理器 */
		sensorManager = (SensorManager) context.getSystemService(Context.SENSOR_SERVICE);
		/* 光线传感器 */
		lightSensor = sensorManager.getDefaultSensor(Sensor.TYPE_LIGHT);
		/* 注册监听 */
		sensorManager.registerListener(sensorListen, lightSensor, SensorManager.SENSOR_DELAY_NORMAL);
		Log.i("langr", "SensorListen open..."+context.toString());
	}
	
	public void closeLight()
	{
		/* 注销传感器 */
		sensorManager.unregisterListener(sensorListen, lightSensor);
		Log.i("langr", "SensorListen close..."+context.toString());
	}

	public float getLight()
	{
		Log.i("langr", "getLight..."+mLight);
		return mLight;
	}

	SensorEventListener sensorListen = new SensorEventListener() {
		@Override
		public void onAccuracyChanged(Sensor sensor, int accuracy) {
			if (sensor.getType() == Sensor.TYPE_LIGHT) {
				//设置将accuracy的值显示到屏幕上
				mAccuracy = accuracy;
			}
			Log.i("langr", "onAccuracyChanged...mLight:"+mLight+"accuracy:"+accuracy);
		}

		@Override
		public void onSensorChanged(SensorEvent event) {
			Log.i("langr", "onSensorChanged...mLight:"+mLight+"mV2:"+event.values[1]+"mV3:"+event.values[2]);
			if (event.sensor.getType() == Sensor.TYPE_LIGHT) {
				float[] values = event.values;
				/* 光线强度值: lux */
				mLight = values[0];
				/* 一次性获取光线值，获取后就注销传感器 */
				//if (mLight > 1) {
					closeLight();
				//}
			}
		}
	};
}
