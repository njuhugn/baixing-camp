package com.baixing.quanleimu;

import java.util.List;

import android.annotation.TargetApi;
import android.app.Activity;
import android.os.Build;
import android.os.Bundle;
import android.support.v4.app.NavUtils;
import android.text.TextUtils;
import android.text.format.DateFormat;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnFocusChangeListener;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.Gallery;
import android.widget.TextView;
import android.widget.Toast;

import com.baixing.quanleimu.AdListData.AdData;

public class AdActivity extends Activity implements OnFocusChangeListener {
	
	private static final String TAG = AdActivity.class.getSimpleName();
	
	private AdData adData;
	private List<String> imgUrlList;
	
	private TextView adTitle;
	private TextView adTime;
	private Gallery imgGallery;
	private TextView adPrice;
	private TextView adAreas;
	private TextView adDescript;
	private TextView adUser;
	private TextView adId;
	private Button adMobile;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_ad);
		// Show the Up button in the action bar.
		setupActionBar();
		
		adData = (AdData) getIntent().getExtras().getSerializable("ad");
		imgUrlList = adData.getImages().getResize180();
		
		adTitle = (TextView) findViewById(R.id.ad_title);
		adTime = (TextView) findViewById(R.id.ad_time);
		imgGallery = (Gallery) findViewById(R.id.ad_gallery);
		adPrice = (TextView) findViewById(R.id.ad_price);
		adAreas = (TextView) findViewById(R.id.ad_areas);
		adDescript = (TextView) findViewById(R.id.ad_description);
		adUser = (TextView) findViewById(R.id.ad_user);
		adId = (TextView) findViewById(R.id.ad_id);
		adMobile = (Button) findViewById(R.id.ad_mobile);
		
		adTitle.setText(adData.getTitle());
		adTime.setText(DateFormat.format("yyyy-MM-dd hh:mm:ss", Long.parseLong(adData.getCreatedTime()) * 1000));
		adPrice.setText(getString(R.string.ad_price) + (TextUtils.isEmpty(adData.get价格()) ? getString(R.string.price_empty) : adData.get价格()));
		adAreas.setText(adData.getAreaNames());
		adDescript.setText(adData.getDescription());
		adUser.setText(getString(R.string.ad_poster) + (TextUtils.isEmpty(adData.getUserNick()) ? R.string.anonymous : adData.getUserNick()));
		adId.setText(getString(R.string.ad_postid) + adData.getId());
		adMobile.setText(isNumberSequence(adData.getContact()) ? adData.getContact() : getString(R.string.mobile_empty));
		imgGallery.setAdapter(new GalleryAdapter(this, imgUrlList));
		imgGallery.setOnFocusChangeListener(this);
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
		getMenuInflater().inflate(R.menu.ad, menu);
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
	
	private OnItemClickListener mItemClickListener = new OnItemClickListener() {

		@Override
		public void onItemClick(AdapterView<?> parent, View view, int position,
				long id) {
			if (imgUrlList != null) {
				Toast.makeText(getParent(), imgUrlList.get(position), Toast.LENGTH_SHORT);
			}
		}

	};
	
	public static boolean isNumberSequence(String target)
    {
    	if (target == null || target.trim().length() == 0)
    	{
    		return false;
    	}
    	target = target.trim();
    	
    	try
    	{
    		Long.parseLong(target); 
    		return true; //If nothing happen.
    	}
    	catch(Throwable t)
    	{
    		return false;
    	}
    	
    }

	@Override
	public void onFocusChange(View arg0, boolean arg1) {
		// TODO Auto-generated method stub
		
	}
}
