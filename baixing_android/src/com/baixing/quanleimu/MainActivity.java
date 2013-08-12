package com.baixing.quanleimu;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.NetworkInfo.State;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.ListView;

public class MainActivity extends Activity {

	static final String TAG = MainActivity.class.getSimpleName();

	private BaixingApp baixingApp;
	private ListView listView;

	private OnItemClickListener mItemClickListener = new OnItemClickListener() {

		@Override
		public void onItemClick(AdapterView<?> parent, View view, int position,
				long id) {
			if (baixingApp.getCategoryData() != null) {
				Log.i(TAG, baixingApp.getCategoryData().getFirstLvCatogories()
						.get(position).toString());
				showSubCategories(position);
			}
		}

	};

	private void showSubCategories(int idx) {
		Intent intent = new Intent(this, SubActivity.class);
		intent.putExtra("subIdx", idx);
		startActivity(intent);
	}

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);

		baixingApp = (BaixingApp) getApplication();
		listView = (ListView) findViewById(R.id.list_view);
		
		if (connectable()) {
			if (baixingApp.getCategoryData() == null) {
				new Thread(new Runnable() {
	
					@Override
					public void run() {
						new UpdateCityAndCateCmd(MainActivity.this, baixingApp)
								.execute();
					}
				}).start();
			}
		}
		
		setViewContent();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

	private void setViewContent() {
		ArrayAdapter<String> adapter = new ArrayAdapter<String>(this,
				android.R.layout.simple_list_item_1,
				baixingApp.getCategories());
		listView.setAdapter(adapter);
		listView.setOnItemClickListener(mItemClickListener);
	}
	
	// 判断网络是否连接成功
	public boolean connectable() {
		ConnectivityManager connManager = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
		if (connManager == null) {
			return false;
		}

		NetworkInfo mobileInfo = connManager
				.getNetworkInfo(ConnectivityManager.TYPE_MOBILE);
		NetworkInfo wifiInfo = connManager
				.getNetworkInfo(ConnectivityManager.TYPE_WIFI);
		State mobile = mobileInfo == null ? null : mobileInfo.getState();
		State wifi = wifiInfo == null ? null : wifiInfo.getState();

		if ((mobile != null && mobile.toString().equals("CONNECTED"))
				|| (wifi != null && wifi.toString().equals("CONNECTED"))) {
			return true;
		}

		return false;
	}
}
