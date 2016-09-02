
package org.langr.util;

import org.langr.digitalphotoframe.FullscreenActivity;
import org.langr.digitalphotoframe.PhotoFrameSettingActivity;
import org.langr.digitalphotoframe.PhotoFrameSettingActivity.PhotoFramePreference;

import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.preference.Preference;
import android.app.Activity;
import android.view.View;
import android.widget.Toast;
import android.util.AttributeSet;
import android.util.Log;


/**
 * select file Preference
 */
public class FileDialogPreference extends Preference
{
	public static final int INTENT_FILES = 1;
	public static final int INTENT_IMAGES = 2;
	
	public FileDialogPreference(Context context, AttributeSet attrs)
	{
		super(context, attrs);
		Log.i("langr", "FileDialogPreference start...");
		//setLayoutResource(R.layout.file_dialog_preference);	
		//setIcon(R.drawable.file_dialog_icon);
	}

	@Override
	protected void onBindView(View view) 
	{
		super.onBindView(view);
	}

	@Override
	protected void onClick() 
	{
		Log.i("langr", "FileDialogPreference onClick...");
		//super.onClick();
		Intent it = new Intent(Intent.ACTION_GET_CONTENT);
		it.setType("image/*");
		it.putExtra("return-data", true);
		//this.getContext().startActivity(it);
		PhotoFrameSettingActivity ps = (PhotoFrameSettingActivity) this.getContext();
		ps.startActivityForResult(it, INTENT_IMAGES);
		Log.i("langr", "FileDialogPreference onClick ok..."+this.getContext().toString());
	}
}
