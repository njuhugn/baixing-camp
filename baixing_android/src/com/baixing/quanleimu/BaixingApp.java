package com.baixing.quanleimu;

import android.app.Application;
import android.content.pm.PackageManager;
import android.content.pm.PackageManager.NameNotFoundException;

import com.baixing.network.api.ApiConfiguration;
import com.baixing.network.api.BaseApiCommand;

public class BaixingApp extends Application {
	
	/*private static final String TAG = BaixingApp.class.getSimpleName();*/ 
	
	private String []categories 	= {"物品交易", "车辆买卖", "房屋租售", "全职招聘", 
			   "兼职招聘", "求职简历", "交友活动", "宠物", 
			   "生活服务", "教育培训"};
	
	private CateListData categoryData;

	@Override
	public void onCreate() {
		// TODO Auto-generated method stub
		super.onCreate();
		
		ApiConfiguration.config("www.baixing.com", null, "api_mobile_android",
				"c6dd9d408c0bcbeda381d42955e08a3f");
		try {
			BaseApiCommand.init(
					Util.getDeviceUdid(getApplicationContext()),
					null,
					Util.getVersion(getApplicationContext()), String.valueOf(getApplicationContext().getPackageManager().getApplicationInfo(getApplicationContext().getPackageName(), PackageManager.GET_META_DATA).metaData.get("UMENG_CHANNEL")),
					"shanghai",
					getPackageName());
		} catch (NameNotFoundException e) {
			BaseApiCommand.init("4759b650db758f27", null, "3.4.1", "androidmarket_umeng", "", "com.quanleimu.activity");
		}
	}
	
	public String[] getCategories() {
		return categories;
	}
	
	public CateListData getCategoryData() {
		return categoryData;
	}
	
	public void setCategories(CateListData cateListData) {
		this.categoryData = cateListData;
	}
}
