package com.baixing.quanleimu;

import java.io.Serializable;
import java.util.List;

import org.apache.commons.lang.StringEscapeUtils;

public class AdListData {
	
	private int count;
	private List<AdData> data;
	
	public List<AdData> getAdList() {
		return data;
	}
	
	@Override
	public String toString() {
		StringBuilder builder = new StringBuilder();
		builder.append("AdListData [count=");
		builder.append(count);
		builder.append(", data=");
		builder.append(data);
		builder.append("]");
		return builder.toString();
	}

	public static class AdData implements Serializable {
		/**
		 * 
		 */
		private static final long serialVersionUID = -5795382621433549596L;
		private String link;
		private String mobile;
		private String id;
		private double lat;
		private double lng;
		private String cityEnglishName;
		private String categoryEnglishName;
		private String categoryFirstLevelEnglishName;
		private String categoryNames;
		private String areaCityLevelId;
		private String areaFirstLevelId;
		private String areaSecondLevelId;
		private String createdTime;
		private String wanted;
		private String userId;
		private String userNick;
		private String areaNames;
		private String status;
		private String imageFlag;
		private String mobileArea;
		private boolean lastOperation;
		private String postMethod;
		private String insertedTime;
		private String title;
		private String description;
		private String contact;
		private String 价格;
		private Image images;
		
		public String getCategoryNames() {
			return categoryNames;
		}
		
		public void setCategoryNames(String categoryNames) {
			this.categoryNames = StringEscapeUtils.unescapeJava(categoryNames);
		}

		public String getAreaNames() {
			return areaNames;
		}

		public void setAreaNames(String areaNames) {
			this.areaNames = StringEscapeUtils.unescapeJava(areaNames);
		}

		public String getMobileArea() {
			return mobileArea;
		}

		public void setMobileArea(String mobileArea) {
			this.mobileArea = StringEscapeUtils.unescapeJava(mobileArea);
		}

		public String getTitle() {
			return title;
		}

		public void setTitle(String title) {
			this.title = StringEscapeUtils.unescapeJava(title);
		}

		public String getDescription() {
			return description;
		}

		public void setDescription(String description) {
			this.description = StringEscapeUtils.unescapeJava(description);
		}

		public String get价格() {
			return 价格;
		}

		public void set价格(String price) {
			this.价格 = StringEscapeUtils.unescapeJava(price);
		}

		public String getCreatedTime() {
			return createdTime;
		}

		public void setCreatedTime(String createdTime) {
			this.createdTime = createdTime;
		}

		public String getInsertedTime() {
			return insertedTime;
		}

		public void setInsertedTime(String insertedTime) {
			this.insertedTime = insertedTime;
		}

		public String getStatus() {
			return status;
		}

		public void setStatus(String status) {
			this.status = status;
		}

		public String getMobile() {
			return mobile;
		}

		public void setMobile(String mobile) {
			this.mobile = mobile;
		}

		public String getUserId() {
			return userId;
		}

		public void setUserId(String userId) {
			this.userId = userId;
		}

		public String getId() {
			return id;
		}

		public void setId(String id) {
			this.id = id;
		}

		public String getContact() {
			return contact;
		}

		public void setContact(String contact) {
			this.contact = contact;
		}

		public Image getImages() {
			return images;
		}

		public void setImages(Image images) {
			this.images = images;
		}

		public String getUserNick() {
			return userNick;
		}

		public void setUserNick(String userNick) {
			this.userNick = userNick;
		}

		@Override
		public String toString() {
			StringBuilder builder = new StringBuilder();
			builder.append("AdData [link=");
			builder.append(link);
			builder.append(", mobile=");
			builder.append(mobile);
			builder.append(", id=");
			builder.append(id);
			builder.append(", lat=");
			builder.append(lat);
			builder.append(", lng=");
			builder.append(lng);
			builder.append(", cityEnglishName=");
			builder.append(cityEnglishName);
			builder.append(", categoryEnglishName=");
			builder.append(categoryEnglishName);
			builder.append(", categoryFirstLevelEnglishName=");
			builder.append(categoryFirstLevelEnglishName);
			builder.append(", categoryNames=");
			builder.append(categoryNames);
			builder.append(", areaCityLevelId=");
			builder.append(areaCityLevelId);
			builder.append(", areaFirstLevelId=");
			builder.append(areaFirstLevelId);
			builder.append(", areaSecondLevelId=");
			builder.append(areaSecondLevelId);
			builder.append(", createdTime=");
			builder.append(createdTime);
			builder.append(", wanted=");
			builder.append(wanted);
			builder.append(", userId=");
			builder.append(userId);
			builder.append(", areaNames=");
			builder.append(areaNames);
			builder.append(", status=");
			builder.append(status);
			builder.append(", imageFlag=");
			builder.append(imageFlag);
			builder.append(", mobileArea=");
			builder.append(mobileArea);
			builder.append(", lastOperation=");
			builder.append(lastOperation);
			builder.append(", postMethod=");
			builder.append(postMethod);
			builder.append(", insertedTime=");
			builder.append(insertedTime);
			builder.append(", title=");
			builder.append(title);
			builder.append(", description=");
			builder.append(description);
			builder.append(", contact=");
			builder.append(contact);
			builder.append(", 价格=");
			builder.append(价格);
			builder.append(", images=");
			builder.append(images);
			builder.append("]");
			return builder.toString();
		}
		
	}
	
	public static class Image implements Serializable {
		/**
		 * 
		 */
		private static final long serialVersionUID = -59152240187604412L;
		private List<String> square;
		private List<String> small;
		private List<String> big;
		private List<String> resize180;
		
		public List<String> getResize180() {
			return resize180;
		}


		public void setResize180(List<String> resize180) {
			this.resize180 = resize180;
		}

		@Override
		public String toString() {
			StringBuilder builder = new StringBuilder();
			builder.append("Image [square=");
			builder.append(square);
			builder.append(", small=");
			builder.append(small);
			builder.append(", big=");
			builder.append(big);
			builder.append(", resize180=");
			builder.append(resize180);
			builder.append("]");
			return builder.toString();
		}
	}

}
