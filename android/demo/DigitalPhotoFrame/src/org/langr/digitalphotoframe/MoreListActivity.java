/** 
 * @file MoreListActivity.java
 * @brief 
 * 
 * Copyright (C) 2014 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package digitalphotoframe
 * @author Langr <hua@langr.org> 2014/11/24 16:23
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: MoreListActivity.java 51 2015-01-08 07:33:07Z loghua@gmail.com $
 */
package org.langr.digitalphotoframe;

import java.util.*;

import android.app.Activity;
import android.app.AlertDialog;
import android.os.Bundle;
import android.preference.PreferenceFragment;
import android.content.Intent;
//import android.view.MotionEvent;
import android.view.View;
import android.widget.ListView;
import android.widget.AdapterView;
import android.widget.SimpleAdapter;
import android.net.Uri;
import android.util.Log;
import org.langr.util.*;

/**
 * 更多列表：
 * 	登陆入口
 * 	相框设置入口
 * 	扫一扫及设置入口
 * 	CRM及设置入口
 */
public class MoreListActivity extends Activity 
{
	private ListView lv;
	/* listview item element key */
	//private String[] item_key = {"item"};
	//private int[] item_id = {R.id.item};

	@Override
	protected void onCreate(Bundle savedInstanceState) 
	{
		super.onCreate(savedInstanceState);
		Log.i("langr", "MoreList onCreate start...");
		
		lv = new ListView(this);
		/* 简单list */
		//lv.setAdapter(new ArrayAdapter<String>(this, android.R.layout.simple_expandable_list_item_1, getListItems()));
		/* 自定义list */
		lv.setAdapter(new SimpleAdapter(this, getListItems(), R.layout.list_item_1, 
					new String[] {"item"}, 
					new int[] {R.id.item}));
		//setContentView(R.layout.more_list);
		setContentView(lv);

		//final View photoFrameView = findViewById(R.id.photo_frame_setting);
		//final View qCodeView = findViewById(R.id.qcode_setting);
		//final View aboutView = findViewById(R.id.about_view);
		//photoFrameView.setOnClickListener(mPhotoFrameOnClickListener);
		lv.setOnItemClickListener(mItemsOnClickListener);
	}

	/**
	 * 点击电子相框设置
	 */
	private View.OnClickListener mPhotoFrameOnClickListener = new View.OnClickListener() {
		@Override
		public void onClick(View v) {
			Intent intent = new Intent();
			intent.setClass(MoreListActivity.this, PhotoFrameSettingActivity.class);
			startActivity(intent);
			return ;
		}
	};

	/**
	 * 点击ListView
	 */
	private AdapterView.OnItemClickListener mItemsOnClickListener = new AdapterView.OnItemClickListener() {
		@Override
		public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
			Log.i("langr", "MoreList onItemClick, position:"+position+" id:"+id+" view:"+view.toString());
			/* 相册设置 */
			if ( position == 0 ) {
				Intent intent = new Intent();
				intent.setClass(MoreListActivity.this, PhotoFrameSettingActivity.class);
				startActivity(intent);
			/* 扫一扫 */
			} else if (position == 1) {
				Intent it = new Intent(Intent.ACTION_SENDTO, Uri.parse("smsto:13026635228"));
				it.putExtra("sms_body", "The SMS text");
				startActivity(it);
			/* 关于 */
			} else if (position == 2) {
				new AlertDialog.Builder(MoreListActivity.this).setTitle("关于")
					.setMessage(FullscreenActivity.VERSION_STRING).show();
				//Intent it2 = new Intent(Intent.ACTION_GET_CONTENT);
				//it2.setType("image/*");
				//startActivity(it2);
			} else if ( position == 3 ) {
				Intent it = new Intent(Intent.ACTION_SENDTO, Uri.parse("smsto:13026635228"));
				it.putExtra("sms_body", "非常好用的电子相册，http://www.langr.org/dpf");
				startActivity(it);
			}
			return ;
		}
	};

	/**
	 * getListData
	 */
	private List<Map<String, Object>> getListItems()
	{
		/*
		List<String> items = new ArrayList<String>();
		items.add("相框设置");
		items.add("扫一扫设置");
		items.add("关于我们");
		*/

		List<Map<String, Object>> items = new ArrayList<Map<String, Object>>();

		Map<String, Object> item = new HashMap<String, Object>();
		item.put("item", "相框设置");
		items.add(item);

		item = new HashMap<String, Object>();
		item.put("item", "扫一扫设置");
		items.add(item);

		item = new HashMap<String, Object>();
		item.put("item", "关于我们");
		items.add(item);

		item = new HashMap<String, Object>();
		item.put("item", "分享");
		items.add(item);
		
		return items;
	}
}
