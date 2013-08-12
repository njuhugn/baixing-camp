package com.baixing.quanleimu;

import java.io.InputStream;
import java.net.URL;
import java.net.URLConnection;
import java.util.List;

import android.content.Context;
import android.content.res.Resources;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.AsyncTask;
import android.os.Handler;
import android.util.Log;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;

public class GalleryAdapter extends BaseAdapter {

	private Context context;
	private List<String> imgList; 
	private Bitmap[] bitmaps;

	public GalleryAdapter(Context context, List<String> imgList) {
		this.context = context;
		this.imgList = imgList;
		this.bitmaps = new Bitmap[imgList.size()];
		for (int i = 0; i < imgList.size(); i++) {
			Resources resources = context.getResources();
			bitmaps[i] = BitmapFactory.decodeResource(resources,
					R.drawable.pic_loading);
		}
		PicLoadTask picLoadTask = new PicLoadTask();
        picLoadTask.execute();
	}

	@Override
	public int getCount() {
		return imgList.size();
	}

	@Override
	public Object getItem(int position) {
		return bitmaps[position];
	}

	@Override
	public long getItemId(int position) {
		return position;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		ImageView imageView = new ImageView(context);
		imageView.setImageBitmap(bitmaps[position]);
		return imageView;
	}

	class PicLoadTask extends AsyncTask<String, Integer, String> {

		// String... 可变长的输入参数，与AsyncTask.exucute()对应
		protected String doInBackground(String... params) {
			// 这里采用一次性全部记载的方法,适合少量图片
			for (int i = 0; i < imgList.size(); i++) {
				try {
					// 从网络获取图片
					URL aryURI = new URL(imgList.get(i));
					URLConnection conn = aryURI.openConnection();
					conn.connect();
					InputStream is = conn.getInputStream();
					Bitmap bitmap = BitmapFactory.decodeStream(is);
					bitmaps[i] = bitmap;
					cwjHandler.post(mUpdateResults); // 发布消息让主线程接收,实现异步线程和主线程的通信
					// notifyDataSetChanged(); //不能直接调用ui操作,这样不是线程安全的
					is.close();
					Thread.sleep(1000); // 模拟延时
				} catch (Exception e) {
					// 处理异常,图片加载失败
					Log.d("lg", e + "");
				}
			}
			return null;
		}

	}

	final Handler cwjHandler = new Handler();

	final Runnable mUpdateResults = new Runnable() {
		public void run() {
			notifyDataSetChanged(); // 不能直接在AsyncTask中调用,因为不是线程安全的
		}
	};

}
