/** 
 * @file LangrReceiver.java
 * @brief 
 * 
 * Copyright (C) 2014 LangR.Org
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * @package service
 * @author Langr <hua@langr.org> 2014/12/07 23:48
 * 
 * $Id: LangrReceiver.java 51 2015-01-08 07:33:07Z loghua@gmail.com $
 */

package org.langr.service;

import android.app.Service;
//import android.os.BroadcastReciver;
import android.os.Bundle;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.util.Log;

/**
 * 广播接收：
 * 	开机自启动相册
 */
public class LangrReceiver extends BroadcastReceiver
{
	@Override
	public void onReceive(Context context, Intent intent)
	{
		Intent serviceIntent = new Intent(context, LangrService.class);
		context.startService(serviceIntent);
		Log.i("langr", "LangrReceiver start...");
	}
}
