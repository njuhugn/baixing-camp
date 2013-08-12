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

		// String... �ɱ䳤�������������AsyncTask.exucute()��Ӧ
		protected String doInBackground(String... params) {
			// �������һ����ȫ�����صķ���,�ʺ�����ͼƬ
			for (int i = 0; i < imgList.size(); i++) {
				try {
					// �������ȡͼƬ
					URL aryURI = new URL(imgList.get(i));
					URLConnection conn = aryURI.openConnection();
					conn.connect();
					InputStream is = conn.getInputStream();
					Bitmap bitmap = BitmapFactory.decodeStream(is);
					bitmaps[i] = bitmap;
					cwjHandler.post(mUpdateResults); // ������Ϣ�����߳̽���,ʵ���첽�̺߳����̵߳�ͨ��
					// notifyDataSetChanged(); //����ֱ�ӵ���ui����,���������̰߳�ȫ��
					is.close();
					Thread.sleep(1000); // ģ����ʱ
				} catch (Exception e) {
					// �����쳣,ͼƬ����ʧ��
					Log.d("lg", e + "");
				}
			}
			return null;
		}

	}

	final Handler cwjHandler = new Handler();

	final Runnable mUpdateResults = new Runnable() {
		public void run() {
			notifyDataSetChanged(); // ����ֱ����AsyncTask�е���,��Ϊ�����̰߳�ȫ��
		}
	};

}
