package org.langr.calendar;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import android.app.Activity;
import android.content.Context;
import android.content.res.Resources;
import android.view.Display;
import android.view.Gravity;
import android.view.View;
import android.view.WindowManager;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.LinearLayout.LayoutParams;
import android.widget.GridView;

/**
 * 用于生成日历展示的GridView布局
 *
 * @author zhouxin@easier.cn
 */
public class CalendarGridView extends GridView {

    /**
     * 当前操作的上下文对象
     */
    private Context mContext;

    /**
     * CalendarGridView 构造器
     *
     * @param context 当前操作的上下文对象
     */
    public CalendarGridView(Context context) {
        super(context);
        mContext = context;

        setGirdView();
    }

    /**
     * 初始化gridView 控件的布局
     */
    private void setGirdView() {
        LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(
                LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);

        setLayoutParams(params);
        setNumColumns(7);// 设置每行列数
        setGravity(Gravity.CENTER_VERTICAL);// 位置居中
        setVerticalSpacing(1);// 垂直间隔
        setHorizontalSpacing(1);// 水平间隔
        setBackgroundColor(getResources().getColor(R.color.calendar_background));

        WindowManager windowManager = ((Activity) mContext).getWindowManager();
        //WindowManager.LayoutParams layoutParams = ((Activity) mContext).getWindow().getAttributes();
        //layoutParams.alpha = (float) 0.8;
        //layoutParams.height = 650;
        //((Activity) mContext).getWindow().setAttributes(layoutParams);
        Display display = windowManager.getDefaultDisplay();
        int i = display.getWidth() / 7;
        int j = display.getWidth() - (i * 7);
        int x = j / 2;
        setPadding(x, 0, 0, 0);// 居中
    }
}
