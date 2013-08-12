package com.baixing.quanleimu;

import java.util.ArrayList;
import java.util.List;

import android.annotation.TargetApi;
import android.app.Activity;
import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.support.v4.app.NavUtils;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.ListView;

import com.baixing.network.api.ApiError;
import com.baixing.network.api.ApiParams;
import com.baixing.network.api.BaseApiCommand.Callback;
import com.baixing.quanleimu.CateListData.SubCategory;

public class SubActivity extends Activity {
	
	static final String TAG = SubActivity.class.getSimpleName();
	
	private List<SubCategory> categories;
	private BaixingApp baixingApp;
	private ListView listView;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_sub);
		// Show the Up button in the action bar.
		setupActionBar();
		
		baixingApp = (BaixingApp) getApplication();
		listView = (ListView) findViewById(R.id.sub_list);
		
		Intent intent = getIntent();
		int index = intent.getIntExtra("subIdx", 0);
		categories = baixingApp.getCategoryData().getFirstLvCatogories().get(index).getChildren();
		
		setViewContent();
	}
	
	public void setViewContent() {
		List<String> cateList = new ArrayList<String>();
		ArrayAdapter<String> listAdapter = new ArrayAdapter<String>(this, android.R.layout.simple_list_item_1, cateList);
		listView.setAdapter(listAdapter);
		listView.setOnItemClickListener(mItemClickListener);
		for (SubCategory category : categories) {
			cateList.add(category.getName());
		}
	}
	
	private OnItemClickListener mItemClickListener = new OnItemClickListener() {

		@Override
		public void onItemClick(AdapterView<?> parent, View view, int position,
				long id) {
			if (categories != null) {
				Log.i(TAG, categories.toString());
				showAdList(categories.get(position).getEnglishName(), "shanghai");
			}
		}

	};
	
	public void showAdList(String categoryEnglishName, String cityEnglishName) {
		Intent intent = new Intent(this, ListActivity.class);
		Bundle bundle = new Bundle();
		bundle.putString("category", categoryEnglishName);
		bundle.putString("city", cityEnglishName);
		intent.putExtras(bundle);
		startActivity(intent);
	}

	/**
	 * Set up the {@link android.app.ActionBar}, if the API is available.
	 */
	@TargetApi(Build.VERSION_CODES.HONEYCOMB)
	private void setupActionBar() {
		if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB) {
			getActionBar().setDisplayHomeAsUpEnabled(true);
		}
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.sub, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		switch (item.getItemId()) {
		case android.R.id.home:
			// This ID represents the Home or Up button. In the case of this
			// activity, the Up button is shown. Use NavUtils to allow users
			// to navigate up one level in the application structure. For
			// more details, see the Navigation pattern on Android Design:
			//
			// http://developer.android.com/design/patterns/navigation.html#up-vs-back
			//
			NavUtils.navigateUpFromSameTask(this);
			return true;
		}
		return super.onOptionsItemSelected(item);
	}
}
