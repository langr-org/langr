package tjuci.edu.dl;

import android.app.ExpandableListActivity;
import android.os.Bundle;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AbsListView;
import android.widget.BaseExpandableListAdapter;
import android.widget.ExpandableListAdapter;
import android.widget.LinearLayout;
import android.widget.TextView;

public class ExpandableTest extends ExpandableListActivity {
	/** Called when the activity is first created. */
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		ExpandableListAdapter ela = new BaseExpandableListAdapter() {
			
			//一级分组
			String[] arrTypes = {"神族兵种","人族兵种","虫族兵种"};
			//二级分组
			String[][] arr = {
					{"狂战士","龙骑士","黑暗圣堂","电兵"},
					{"小狗","刺蛇","飞龙","自爆飞机"},
					{"机枪兵","护士MM","幽灵"}
			};
			@Override
			public boolean isChildSelectable(int groupPosition, int childPosition) {
				// TODO Auto-generated method stub
				return true;
			}
			
			@Override
			public boolean hasStableIds() {
				// TODO Auto-generated method stub
				return true;
			}
			
			@Override
			public View getGroupView(int groupPosition, boolean isExpanded,
					View convertView, ViewGroup parent) {
				// TODO Auto-generated method stub
				LinearLayout ll = new LinearLayout(ExpandableTest.this);
				ll.setOrientation(0);
				TextView t = getTextView();
				t.setText(getGroup(groupPosition).toString());
				ll.addView(t);
				return ll;
			}
			
			@Override
			public long getGroupId(int groupPosition) {
				// TODO Auto-generated method stub
				return groupPosition;
			}
			
			@Override
			public int getGroupCount() {
				// TODO Auto-generated method stub
				return arrTypes.length;
			}
			
			@Override
			public Object getGroup(int groupPosition) {
				// TODO Auto-generated method stub
				return arrTypes[groupPosition];
			}
			
			@Override
			public int getChildrenCount(int groupPosition) {
				// TODO Auto-generated method stub
				return arr[groupPosition].length;
			}
			
			@Override
			public View getChildView(int groupPosition, int childPosition,
					boolean isLastChild, View convertView, ViewGroup parent) {
				// TODO Auto-generated method stub
				TextView tv = getTextView();
				tv.setText(getChild(groupPosition, childPosition).toString());
				return tv;
			}
			
			@Override
			public long getChildId(int groupPosition, int childPosition) {
				// TODO Auto-generated method stub
				return childPosition;
			}
			
			@Override
			public Object getChild(int groupPosition, int childPosition) {
				// TODO Auto-generated method stub
				return arr[groupPosition][childPosition];
			}
			
			public TextView getTextView(){
				AbsListView.LayoutParams lp = new AbsListView.LayoutParams(ViewGroup.LayoutParams.FILL_PARENT,64);
				TextView tv = new TextView(ExpandableTest.this);
				tv.setLayoutParams(lp);
				tv.setPadding(36,0,0,0);
				tv.setTextSize(24);
				return tv;
			}
		};
		setListAdapter(ela);
	}
}