package com.baixing.quanleimu;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;

import android.annotation.TargetApi;
import android.app.Activity;
import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.support.v4.app.NavUtils;
import android.text.format.DateFormat;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.Toast;

import com.baixing.network.api.ApiError;
import com.baixing.network.api.ApiParams;
import com.baixing.network.api.BaseApiCommand.Callback;
import com.baixing.quanleimu.AdListData.AdData;
import com.google.baixing.GsonBuilder;

public class ListActivity extends Activity implements Callback, VadListLoader.Callback {
	
	private static final String TAG = ListActivity.class.getSimpleName();
	
	private List<AdData> adList;
	private ListView listView;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_list);
		// Show the Up button in the action bar.
		setupActionBar();
		
		listView = (ListView) findViewById(R.id.ad_list);
		
		if (adList == null) {
			Bundle bundle = getIntent().getExtras();
			executeGetAdsCommand(bundle.getString("category"), bundle.getString("city"));
		} else {
			setViewContent();
		}
	}
	
	private void executeGetAdsCommand(String categoryEnglishName, String cityEnglishName) {
		/*ApiParams params = new ApiParams();
		params.addParam("categoryEnglishName", categoryEnglishName);
		params.addParam("cityEnglishName", cityEnglishName);
		
		BaseApiCommand.createCommand("category_meta_filter", true, params).execute(this, this);*/
		PostParamsHolder filterParamHolder = new PostParamsHolder();
		filterParamHolder.put("cityEnglishName", cityEnglishName, cityEnglishName);
		filterParamHolder.put("categoryEnglishName", categoryEnglishName, categoryEnglishName);
		filterParamHolder.put("status", "" + 0, "" + 0);
		ApiParams basicParams = new ApiParams();
		basicParams.addParam("query", filterParamHolder.toUrlString());
		
		VadListLoader goodsListLoader = new VadListLoader(basicParams, this, null, new AdList());
		goodsListLoader.setRuntime(true);
		goodsListLoader.startFetching(getApplicationContext(), true, false);
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
		getMenuInflater().inflate(R.menu.list, menu);
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
	
	Handler handler = new Handler() {

		@Override
		public void handleMessage(Message msg) {
			switch (msg.what) {
			case VadListLoader.MSG_FIRST_FAIL:
				// TODO 
				break;
			case VadListLoader.MSG_FINISH_GET_FIRST:
				adList = new GsonBuilder().create().fromJson((String)msg.obj, AdListData.class).getAdList();
				setViewContent();
				break;
			case VadListLoader.MSG_NO_MORE:
				// TODO
				break;
			case VadListLoader.MSG_FINISH_GET_MORE:
				// TODO
				break;
			default:
				// TODO
				break;
			}
		}
		
	};

	@Override
	public void onRequestComplete(int respCode, Object data) {
		Message message = handler.obtainMessage(respCode, data);
		message.sendToTarget();
	}
	
	private void setViewContent() {
		List<String> adTitleList = new ArrayList<String>();
		ArrayAdapter<String> listAdapter = new ArrayAdapter<String>(this, android.R.layout.simple_list_item_1, adTitleList);
		listView.setAdapter(listAdapter);
		listView.setOnItemClickListener(mItemClickListener);
		for (AdData ad : adList) {
			adTitleList.add(ad.getTitle() + "\n"
					+ ad.getAreaNames() + "\n"
					+ ((ad.get价格() != null) ? (ad.get价格() + "\n") : "")
					+ DateFormat.format("yyyy-MM-dd hh:mm:ss", new Date(Long.parseLong(ad.getInsertedTime()) * 1000))
					);
		}
	}
	
	private OnItemClickListener mItemClickListener = new OnItemClickListener() {

		@Override
		public void onItemClick(AdapterView<?> parent, View view, int position,
				long id) {
			if (adList != null) {
				Log.i(TAG, adList.toString());
				showAdDetail(position);
				/*Toast.makeText(getApplicationContext(), adList.get(position).getDescription(), Toast.LENGTH_SHORT).show();*/
			}
		}

	};
	
	private void showAdDetail(int index) {
		Intent intent = new Intent(this, AdActivity.class);
		Bundle bundle = new Bundle();
		bundle.putSerializable("ad", adList.get(index));
		intent.putExtras(bundle);
		startActivity(intent);
	}

	@Override
	public void onNetworkDone(String apiName, String responseData) {
		// TODO Auto-generated method stub
		
	}

	@Override
	public void onNetworkFail(String apiName, ApiError error) {
		// TODO Auto-generated method stub
		
	}

}
