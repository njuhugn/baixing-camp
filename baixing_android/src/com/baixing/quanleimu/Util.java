package com.baixing.quanleimu;

import java.io.File;
import java.io.FileOutputStream;
import java.util.UUID;

import android.content.Context;
import android.content.SharedPreferences;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.content.pm.PackageManager.NameNotFoundException;
import android.provider.Settings.Secure;
import android.telephony.TelephonyManager;

public class Util {
	
	private static String PREF_DEVICE_ID = "bx_share_deviceID";
	private static String PREF_KEY_DEVICE_ID = "pref_key_device";
	private static String DEVICE_ID = "";
	
    static public String getDeviceUdid(Context context) {
    	
    	/**
    	 * Firstly, check if memory exists. 
    	 */
    	if (DEVICE_ID != null && DEVICE_ID.length() > 0)
    	{
    		return DEVICE_ID;
    	}
    	
    	/**
    	 * Then, check if we have saved the preference.
    	 */
    	SharedPreferences pref = context.getSharedPreferences(PREF_DEVICE_ID, Context.MODE_PRIVATE);
    	if (pref != null && pref.contains(PREF_KEY_DEVICE_ID))
    	{
    		DEVICE_ID = pref.getString(PREF_KEY_DEVICE_ID, null);
    	}
    	
    	if (DEVICE_ID != null && DEVICE_ID.length() > 0)
    	{
    		return DEVICE_ID;
    	}

    	/**
    	 * And last, get ANDROID_ID or device id, or random id if we cannot get any unique id from android system. 
    	 */
    	try
    	{
    		DEVICE_ID = Secure.getString(context.getContentResolver(), Secure.ANDROID_ID);
    		
    		/**
    		 * --> 9774d56d682e549c is an android system bug.
    		 * --> null or "null" means cannot get android id.
    		 */
    		if ("9774d56d682e549c".equals(DEVICE_ID) || DEVICE_ID == null || "null".equalsIgnoreCase(DEVICE_ID.trim())) {
    			final String deviceId = ((TelephonyManager) context.getSystemService( Context.TELEPHONY_SERVICE )).getDeviceId();
    			String uuid = deviceId!=null && !"null".equalsIgnoreCase(deviceId.trim()) ? UUID.nameUUIDFromBytes(deviceId.getBytes("utf8")).toString() : UUID.randomUUID().toString();
    			
    			DEVICE_ID = uuid;
    			return uuid;
    		}
    	}
    	catch(Throwable t)
    	{
    		DEVICE_ID = System.currentTimeMillis() + ""; //If any exception occur, use system current time as unique id.
    		t.printStackTrace();
    	}
    	finally
    	{
    		if (pref != null)
    		{
    			pref.edit().putString(PREF_KEY_DEVICE_ID, DEVICE_ID).commit(); 
    		}
    	}
    	
    	return DEVICE_ID;

    }
    
    public static String getVersion(Context ctx){
		PackageManager packageManager = ctx.getPackageManager();
		// getPackageName()是你当前类的包名，0代表是获取版本信息
		PackageInfo packInfo;
		try {
			packInfo = packageManager.getPackageInfo(ctx.getPackageName(), 0);
			return packInfo.versionName;
		} catch (NameNotFoundException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}
		return "";
	}
	
	/**
	 * 保存json数据和timstamp(秒数)至手机内存。会检查json的完整性。
	 * @param context
	 * @param file     
	 * @param json      json String
	 * @param timestamp 秒数
	 * @return
	 */
	public static String saveJsonAndTimestampToLocate(Context context, String file, String json, long timestamp) {
		if (json == null)
			return "data invalid";
		
		json = json.trim();
		if ((json.startsWith("[") && json.endsWith("]")) ||
			(json.startsWith("{") && json.endsWith("}")) ) {
			String s = String.format("%d,%s", timestamp, json);
			saveDataToFile(context, null, file, s.getBytes());
			return "保存成功";
		}else{
			return "data invalid";
		}
	}
	
	public static String saveDataToFile(Context context, String dir, String file, byte[] data)
	{
		return saveDataToFile( context,  dir,  file, data, false);
	}
	
	public static String saveDataToFile(Context context, String dir, String file, byte[] data, boolean append)
	{
		if (file == null || data == null || data.length == 0 || context == null)
		{
			return null;
		}
		
		String dirPath = context.getFilesDir().getAbsolutePath();
		if (dir != null)
		{
			dirPath  = dir.startsWith(File.separator) ? dirPath + dir : dirPath + File.separator + dir;
			
			File dirFile = new File(dirPath);
			dirFile.mkdirs();
			
			if (!dirFile.exists())
			{
				return null;
			}
		}
		
		
		String filePath = dirPath.endsWith(File.separator) ? dirPath + file : dirPath + File.separator + file; 
		FileOutputStream os =null;
		try
		{
			os = new FileOutputStream(new File(filePath), append);
			os.write(data);
			os.flush();
			os.close();
			
			return filePath;
		}
		catch(Throwable t)
		{
		}
		finally
		{
			if (os != null)
			{
				try
				{
					os.flush();
					os.close();
				}
				catch(Throwable t)
				{
					//Ignor.
				}
			}
		}
		
		return null;
	}

}
