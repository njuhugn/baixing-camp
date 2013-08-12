package com.baixing.quanleimu;

import android.app.Activity;
import android.content.Context;
import android.text.TextUtils;
import android.util.Log;
import android.widget.Toast;

import com.baixing.network.api.ApiError;
import com.baixing.network.api.ApiParams;
import com.baixing.network.api.BaseApiCommand;
import com.baixing.network.api.BaseApiCommand.Callback;
import com.google.baixing.GsonBuilder;

public class UpdateCityAndCateCmd implements Callback {
	
	static final String TAG = UpdateCityAndCateCmd.class.getSimpleName();
	
	private Activity activity;
	private BaixingApp application;
	/*private long cityUpdateTime;
	private long cateUpdateTime;*/
	
	public UpdateCityAndCateCmd(Activity activity, BaixingApp application) {
		this.activity = activity;
		this.application = application;
	}
	
	public void execute() {
		ApiParams params = new ApiParams();
		params.addParam("cityEnglishName", "shanghai");
		
		Log.d(TAG, "UpdateCityAndCateCmd execute");
		BaseApiCommand.createCommand("category_list", true, params).execute(activity, this);
	}

	@Override
	public void onNetworkDone(String apiName, String responseData) {
		Log.d(TAG, "UpdateCityAndCateCmd onNetworkDone");
		if ("category_list".equals(apiName)) {
			Log.d(TAG, "UpdateCityAndCateCmd category_list");
			if (!TextUtils.isEmpty(responseData)) {
				Log.d(TAG, "UpdateCityAndCateCmd reponseData");
				/*Util.saveJsonAndTimestampToLocate(context, "saveFirstStepCate",
						responseData, 0);*/
				application.setCategories(new GsonBuilder().create().fromJson(responseData, CateListData.class));
				Log.i(TAG, application.getCategories().toString());
			}
		}
		activity.runOnUiThread(new Runnable() {
			
			@Override
			public void run() {
				Toast.makeText(activity, R.string.data_ready, Toast.LENGTH_SHORT).show();
			}
		});	
	}

	@Override
	public void onNetworkFail(String apiName, ApiError error) {
		Log.d(TAG, "UpdateCityAndCateCmd onNetworkFail");
	}

}
