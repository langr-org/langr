package org.langr.digitalphotoframe;

import java.io.File;
import java.util.ArrayList;
import java.util.List;

import android.app.ListActivity;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.LayoutInflater;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.Toast;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.util.Log;

public class FileSelectManager extends ListActivity 
{
	private List<String> items = null;
	private List<String> paths = null;
	private String rootPath = "/";
	private String curPath = "/";
	private TextView mPath;

	public final static int INTENT_DIR = 99;

	@Override
	public void onCreate(Bundle icicle) 
	{
		super.onCreate(icicle);
		Log.i("langr", "FileSelect");
		//Log.i("langr", "FileSelectManager start..."+icicle.toString());

		requestWindowFeature(Window.FEATURE_NO_TITLE);

		setContentView(R.layout.fileselect);
		mPath = (TextView) findViewById(R.id.mPath);
		Button buttonConfirm = (Button) findViewById(R.id.buttonConfirm);
		buttonConfirm.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				/* 返回父Activity */
				//Intent it = new Intent(FileSelectManager.this, MainAcivity.class);
				//Intent it = new Intent();
				Intent it = getIntent();
				Bundle bundle = new Bundle();
				bundle.putString("file", curPath);
				it.putExtras(bundle);
				setResult(INTENT_DIR, it);
				finish();
			}
		});
		Button buttonCancle = (Button) findViewById(R.id.buttonCancle);
		buttonCancle.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {
				Intent it = getIntent();
				//Bundle bundle = new Bundle();
				//bundle.putString("file", curPath);
				//it.putExtras(bundle);
				setResult(INTENT_DIR, it);
				finish();
			}
		});
		Log.i("langr", "FileSelectManager start...5"+rootPath);
		getFileDir(rootPath);
	}

	private void getFileDir(String filePath) 
	{
		File f = null;
		File[] files = null;
		try {
			f = new File(filePath);
			files = f.listFiles();
		} catch (Exception e) {
			Toast.makeText(getBaseContext(), e.toString(),Toast.LENGTH_SHORT).show();
			Log.i("langr", "access error:"+filePath);
		}
		if ( files == null ) {
			Toast.makeText(getBaseContext(), "无权访问"+filePath,Toast.LENGTH_SHORT).show();
			Log.i("langr", "files null:"+filePath);
			return ;
		}
		
		mPath.setText(filePath);
		paths = new ArrayList<String>();
		items = new ArrayList<String>();

		if (!filePath.equals(rootPath)) {
			items.add("b1");
			paths.add(rootPath);
			items.add("b2");
			paths.add(f.getParent());
		}
		for (int i = 0; i < files.length; i++) {
			File file = files[i];
			items.add(file.getName());
			paths.add(file.getPath());
		}

		setListAdapter(new FilesAdapter(this, items, paths));
	}

	/**
	 * @brief 获取目录中指定类型的文件
 	 * @param type	"image/*..."
	 * @return ArrayList files
	 */
	public static List<String> getDirFiles(String filePath, String type)
	{
		File f = null;
		File[] files = null;
		try {
			f = new File(filePath);
			files = f.listFiles();
		} catch (Exception e) {
			Log.i("langr", "access error:"+filePath);
		}
		if ( files == null ) {
			//Toast.makeText(getBaseContext(), "无权访问"+filePath,Toast.LENGTH_SHORT).show();
			Log.i("langr", "files null:"+filePath);
			return null;
		}
		
		List<String> items = new ArrayList<String>();

		for (int i = 0; i < files.length; i++) {
			File file = files[i];
			/* 全部类型文件 */
			if ( type == null ) {
				//items.add(file.getName());
				items.add(file.getPath());
			/* 指定类型文件 */
			} else if ( getMIMEType(files[i]).equals(type) ) {
				items.add(file.getPath());
			}
		}

		return items;
	}

	@Override
	protected void onListItemClick(ListView l, View v, int position, long id) 
	{
		File file = new File(paths.get(position));
		if (file.isDirectory()) {
			curPath = paths.get(position);
			getFileDir(paths.get(position));
		} else {
			openFile(file);
		}
	}

	private void openFile(File f) 
	{
		Intent intent = new Intent();
		intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
		intent.setAction(android.content.Intent.ACTION_VIEW);

		String type = getMIMEType(f);
		intent.setDataAndType(Uri.fromFile(f), type);
		startActivity(intent);
	}

	public static String getMIMEType(File f) 
	{
		String type = "";
		String fName = f.getName();
		String end = fName.substring(fName.lastIndexOf(".") + 1, fName.length()).toLowerCase();

		if (end.equals("m4a") || end.equals("mp3") || end.equals("mid")
				|| end.equals("xmf") || end.equals("ogg") || end.equals("wav")) {
			type = "audio";
		} else if (end.equals("3gp") || end.equals("mp4")) {
			type = "video";
		} else if (end.equals("jpg") || end.equals("gif") || end.equals("png")
				|| end.equals("jpeg") || end.equals("bmp")) {
			type = "image";
		} else {
			type = "*";
		}
		type += "/*";
		return type;
	}

	public class FilesAdapter extends BaseAdapter
	{
		private LayoutInflater mInflater;
		private Bitmap mIcon1;
		private Bitmap mIcon2;
		private Bitmap mIcon3;
		private Bitmap mIcon4;
		private List<String> items;
		private List<String> paths;
		public FilesAdapter(Context context,List<String> it,List<String> pa)
		{
			mInflater = LayoutInflater.from(context);
			items = it;
			paths = pa;
			mIcon1 = BitmapFactory.decodeResource(context.getResources(),R.drawable.back01);
			mIcon2 = BitmapFactory.decodeResource(context.getResources(),R.drawable.back02);
			mIcon3 = BitmapFactory.decodeResource(context.getResources(),R.drawable.folder);
			mIcon4 = BitmapFactory.decodeResource(context.getResources(),R.drawable.doc);
		}
		
		public int getCount()
		{
			return items.size();
		}

		public Object getItem(int position)
		{
			return items.get(position);
		}
		
		public long getItemId(int position)
		{
			return position;
		}
		
		public View getView(int position,View convertView,ViewGroup parent)
		{
			ViewHolder holder;
				
			if (convertView == null) {
				convertView = mInflater.inflate(R.layout.file_row, null);
				holder = new ViewHolder();
				holder.text = (TextView) convertView.findViewById(R.id.text);
				holder.icon = (ImageView) convertView.findViewById(R.id.icon);
						
				convertView.setTag(holder);
			} else {
				holder = (ViewHolder) convertView.getTag();
			}

			File f = new File(paths.get(position).toString());
			if (items.get(position).toString().equals("b1")) {
				holder.text.setText("返回根目录..");
				holder.icon.setImageBitmap(mIcon1);
			} else if (items.get(position).toString().equals("b2")) {
				holder.text.setText("返回上一层..");
				holder.icon.setImageBitmap(mIcon2);
			} else {
				holder.text.setText(f.getName());
				if (f.isDirectory()) {
					holder.icon.setImageBitmap(mIcon3);
				} else {
					holder.icon.setImageBitmap(mIcon4);
				}
			}
			return convertView;
		}

		private class ViewHolder
		{
			TextView text;
			ImageView icon;
		}
	}
}
