
package org.langr.util;

import org.langr.digitalphotoframe.FileSelectManager;
import org.langr.digitalphotoframe.FullscreenActivity;
import org.langr.digitalphotoframe.MoreListActivity;
import org.langr.digitalphotoframe.PhotoFrameSettingActivity;

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
public class DirDialogPreference extends Preference
{
	public static final int INTENT_FILES = 1;
	public static final int INTENT_IMAGES = 2;
	
	public DirDialogPreference(Context context, AttributeSet attrs)
	{
		super(context, attrs);
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
		//super.onClick();
		Intent it = new Intent();
		it.setClass(this.getContext(), FileSelectManager.class);
		PhotoFrameSettingActivity ps = (PhotoFrameSettingActivity) this.getContext();
		ps.startActivityForResult(it, FileSelectManager.INTENT_DIR);
		//ps.startActivity(it);
	}
}
