package com.baixing.quanleimu;

import java.util.List;

import org.apache.commons.lang.StringEscapeUtils;

public class CateListData {
	
	private String name;
	private String englishName;
	private String level;
	private List<SubCategory> children;
	
	public List<SubCategory> getFirstLvCatogories() {
		return children;
	}
	
	@Override
	public String toString() {
		StringBuilder builder = new StringBuilder();
		builder.append("CateListData [name=");
		builder.append(name);
		builder.append(", englishName=");
		builder.append(englishName);
		builder.append(", level=");
		builder.append(level);
		builder.append(", children=");
		builder.append(children);
		builder.append("]");
		return builder.toString();
	}

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = StringEscapeUtils.unescapeJava(name);
	}

	public static class SubCategory {
		private String name;
		private String englishName;
		private String parentEnglishName;
		private String level;
		private String shortname;
		private List<SubCategory> children;
		
		@Override
		public String toString() {
			StringBuilder builder = new StringBuilder();
			builder.append("Children [name=");
			builder.append(name);
			builder.append(", englishName=");
			builder.append(englishName);
			builder.append(", parentEnglishName=");
			builder.append(parentEnglishName);
			builder.append(", level=");
			builder.append(level);
			builder.append(", shortname=");
			builder.append(shortname);
			builder.append(", children=");
			builder.append(children);
			builder.append("]");
			return builder.toString();
		}

		public String getName() {
			return name;
		}
		
		public void setName(String name) {
			this.name = StringEscapeUtils.unescapeJava(name);
		}
		
		public String getShortname() {
			return shortname;
		}
		
		public void setShortname(String shortname) {
			this.shortname = StringEscapeUtils.unescapeJava(shortname);
		}

		public String getEnglishName() {
			return englishName;
		}

		public void setEnglishName(String englishName) {
			this.englishName = englishName;
		}

		public String getParentEnglishName() {
			return parentEnglishName;
		}

		public void setParentEnglishName(String parentEnglishName) {
			this.parentEnglishName = parentEnglishName;
		}

		public String getLevel() {
			return level;
		}

		public void setLevel(String level) {
			this.level = level;
		}

		public List<SubCategory> getChildren() {
			return children;
		}

		public void setChildren(List<SubCategory> children) {
			this.children = children;
		}
		
		
	}

}
