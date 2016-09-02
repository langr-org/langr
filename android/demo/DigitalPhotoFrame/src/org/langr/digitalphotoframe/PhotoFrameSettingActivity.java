/** 
 * @file PhotoFrameSettingActivity.java
 * @brief 
 * 
 * Copyright (C) 2014 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package digitalphotoframe
 * @author Langr <hua@langr.org> 2014/11/24 18:06
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: PhotoFrameSettingActivity.java 51 2015-01-08 07:33:07Z loghua@gmail.com $
 */
package org.langr.digitalphotoframe;

import android.preference.PreferenceFragment;
import android.preference.PreferenceManager;
import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
//import android.preference.PreferenceActivity;
import android.os.Bundle;
import android.util.Log;
import android.widget.Toast;
import org.langr.util.*;

/**
 * 相框设置
 */
public class PhotoFrameSettingActivity extends Activity
{
	@Override
	public void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		Log.i("langr", "PhotoFrameSettingActivity onCreate start...");
		getFragmentManager().beginTransaction().replace(android.R.id.content, new PhotoFramePreference()).commit();
	}
	
	/**
	 * 相框设置
	 */
	public class PhotoFramePreference extends PreferenceFragment
	{
		private final String PHOTOS_DIR = "photos_dir";
		private DirDialogPreference mDirPreference;

		@Override
		public void onCreate(Bundle savedInstanceState)
		{
			super.onCreate(savedInstanceState);
			addPreferencesFromResource(R.xml.preferences);
			mDirPreference = (DirDialogPreference) getPreferenceScreen().findPreference(PHOTOS_DIR);
		}

		@Override
		public void onResume()
		{
			super.onResume();
			Log.i("langr", "PhotoFramePreferenceFragment onResume ...");
			mDirPreference.setSummary(getPreferenceScreen().getSharedPreferences().getString(PHOTOS_DIR, "/"));
			//sharedPreferences.getString(PHOTOS_DIR, "/");
		}
	}
	
	/**
	 * 从其他Activity返回
	 * 设置界面返回
	 */
	protected  void onActivityResult(int requestCode, int resultCode, Intent data)
	{
		Log.i("langr", "PhotoFrameSetting Activity return ok...req:"+requestCode+"res:"+resultCode+" d:"+data.toString());
		/* DEL: 选择图片 */
		if (requestCode == FileDialogPreference.INTENT_IMAGES && data != null) {
			String uri = data.getData().toString();
			/* 取属于整个应用程序的SharedPreferences, 并保存Intent返回选择的文件 */ 
			Log.i("langr", "PhotoFrameSetting Activity return ok...req:"+requestCode+"res:"+uri);
			SharedPreferences settings = PreferenceManager.getDefaultSharedPreferences(this);
			settings.edit().putString("photos", uri).commit();
		/* 选择图片文件夹 */
		} else if (requestCode == FileSelectManager.INTENT_DIR) {
			Bundle bundle = null;
			if (data != null && (bundle=data.getExtras()) != null) {
				Log.i("langr", "FileSelectManager return ok...res:"+bundle.getString("file"));
				SharedPreferences settings = PreferenceManager.getDefaultSharedPreferences(this);
				settings.edit().putString("photos_dir", bundle.getString("file")).commit();
			}
		} else {
			/* 其他Intent返回的结果 */
		}
		
		//super.onActivityResult(requestCode, resultCode, data);
	}
}
