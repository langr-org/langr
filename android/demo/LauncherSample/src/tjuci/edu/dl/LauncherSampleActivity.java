package tjuci.edu.dl;

import android.app.LauncherActivity;
import android.content.Intent;
import android.os.Bundle;
import android.widget.ArrayAdapter;

public class LauncherSampleActivity extends LauncherActivity {
	
	String[] names= {"设置程序参数","查看星际种族"};
	Class<?>[] clazzs = {PrefernceTest.class,ExpandableTest.class};
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        ArrayAdapter<String> arr = new ArrayAdapter<String>(this, android.R.layout.simple_list_item_1,names);
        this.setListAdapter(arr);
    }
    @Override
	protected Intent intentForPosition(int position) {
		// TODO Auto-generated method stub
		return new Intent(this, clazzs[position]);
	}
}