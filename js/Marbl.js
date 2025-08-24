/*
 *		MARBL
 *		0.9.20 | (c) C:Amie - www.c-amie.co.uk 2018 - 2024.
 *
 *		MARBL contains three classes:
 *			- MarblLink used as a generator class for affiliate link types and generates:
 *				- MarblAmazonLink which provides Amazon Affiliate Link functionality
 *				- MarblEbayLink which provides eBay Parnter Network Link functionality
 */

/*
 *	MARBL GENERATOR
 *  Usage: <script type="text/javascript">var marblLink = new MarblLink('ebay', '/api/import/marbl/');</script>
 *  Note: This is a Generator Class Only
 */
 function MarblLink(strLinkType, strAssetsPath) {
	/* Fields */
	if (strAssetsPath === undefined) {
		var strAssetsPath = this.getRepositoryPath();
	}
	switch (strLinkType.toLowerCase()) {
		case 'amazon':
			return new MarblAmazonLink(strAssetsPath);
			break;
		case 'ebay':
			return new MarblEbayLink(strAssetsPath);
			break;
		default:
			return null;
			break;
	}
 }

	MarblLink.prototype.getScriptPath = function() {
		var strFilename = 'Marbl.js';
		var scriptElements = document.getElementsByTagName('script');
		for (var i = 0; i < scriptElements.length; i++) {
			var source = scriptElements[i].src;
			if (source.indexOf(strFilename) > -1) {
				var location = source.substring(0, source.indexOf(strFilename)) + strFilename;
				return location;
			}
		}
		return false;
	}

	MarblLink.prototype.getRepositoryPath = function() {
		var strPath = this.getScriptPath();
		if (strPath.substring((strPath.length - 11), strPath.length).toLowerCase() == 'js/marbl.js') {
			strPath = strPath.substring(0, (strPath.length - 11));
			strPath = strPath.replace(location.origin, '');
			return strPath;
		}
	}


/*
 *
 *		AMAZON
 *
 */
 function MarblAmazonLink(strAssetsPath) {
	/* Emum */
	this.IMAGE						= 1;
	this.TEXT						= 2;
	this.BOTH						= 3;

	this.INPLACE					= 1;
	this.CONTAINER					= 2;

	this.TOP						= 1;
	this.BOTTOM						= 2;

	/* Fields */
	this.AssociateIds				= new Array(new Array(), new Array());
	this.AssociateIdsCount			= 0;

	this.IssueCount					= 0;	// How many codes this instance has created
	this.AssetsPath					= strAssetsPath;

	this.DisplayFlags				= true;
	this.OpenInNewWindow			= true;
	this.LinksNoFollow				= true;

	this.DisclaimerText				= null;
	this.DisclaimerPosition			= this.BOTTOM;

	this.RenderMode					= this.INPLACE
	this.ContainerId				= null;
	this.PrintDebug					= false;
 }
//	MarblAmazonLink.prototype.synchronousGet = function(strTargetUrl)

	MarblAmazonLink.prototype.addAssociateId = function(strCountry, strId) {
		if ((strCountry.length == 2) && (strId != '')) {
			this.AssociateIds[0][this.AssociateIdsCount] = strCountry.toUpperCase();
			this.AssociateIds[1][this.AssociateIdsCount] = strId.toLowerCase();
			this.AssociateIdsCount++;
		}
	}

	MarblAmazonLink.prototype.getAssociateId = function(strCountry) {
		if (this.AssociateIdsCount > 0) {
			for (var i = 0; i < this.AssociateIdsCount; i++) {
				if (this.AssociateIds[0][i] == strCountry) {
					return this.AssociateIds[1][i];
				}
			}
		}
		return '';
	}

	MarblAmazonLink.prototype.setDisclaimer = function(iDisclaimerPosition, strDiscalimerText) {
		this.DisclaimerPosition		= iDisclaimerPosition;
		this.DisclaimerText			= strDiscalimerText;
	}

	MarblAmazonLink.prototype.getUrl = function(strCountry, strFreetext, strAsin, strIsbn) {
		var strTag = '';
		var strDomain = this.getAmazonDomain(strCountry);
		var strAssociateId = this.getAssociateId(strCountry);
		
		if (strDomain != undefined) {
			if (strAssociateId != 'null' && strAssociateId != '') {
				strTag = '&tag=' + strAssociateId;
			}
		
			strFreetext = strFreetext.replace("\+", '%2b');
			strFreetext = strFreetext.replace(/\s/g, "%20");
			strFreetext = strFreetext.replace(/%20/g, "+");

			if (strFreetext != '') {
				return 'https://www.amazon.' + strDomain + '/s/ref=as_li_ss_tl?url=search-alias=aps&field-keywords=' + encodeURI(strFreetext.replace('&', '%26')) + '&rh=i:aps,k:' + encodeURI(strFreetext.replace('&', '%26')) + strTag;
			}
			if (strAsin != '') {
				return 'https://www.amazon.' + strDomain + '/gp/product/' + encodeURI(strAsin) + '/?creativeASIN=' + encodeURI(strAsin) + strTag;
			}
			if (strIsbn != '') {
				return 'https://www.amazon.' + strDomain + '/s?k=' + encodeURI(strIsbn) + strTag;
			}
		}
	}

	MarblAmazonLink.prototype.getAmazonDomain = function(strCountry) {
		switch (strCountry) {
			case 'AE':			// UAE
				return 'ae';
				break;
			case 'AU':			// Australia
				return 'com.au';
				break;
			case 'BE':			// Belgium
				return 'com.be';
				break;
			case 'BR':
				return 'com.br';
				break;
			case 'CA':
				return 'ca';
				break;
			case 'CN':
				return 'cn';
				break;
			case 'DE':
				return 'de';
				break;
			case 'ES':
				return 'es';
				break;
			case 'FR':
				return 'fr';
				break;
			case 'IN':
				return 'in';
				break;
			case 'IT':
				return 'it';
				break;
			case 'JP':
				return 'jp';
				break;
			case 'MX':
				return 'com.mx';
				break;
			case 'NL':			// Netherlands
				return 'nl';
				break;
			case 'PL':			// Poland
				return 'pl';
				break;
			case 'SA':			// Saudi Arabia
				return 'sa';
				break;
			case 'SE':			// Sweden
				return 'se';
				break;
			case 'SG':			// Singapore
				return 'sg';
				break;
			case 'UK':
				return 'co.uk';
				break;
			case 'US':
				return 'com';
				break;
		}
	}

	MarblAmazonLink.prototype.createGuid = function() {
	   strOut = this.S4() + this.S4() + '-' + this.S4() + '-4' + this.S4().substr(0,3) + '-' + this.S4() + '-' + this.S4() + this.S4() + this.S4();
	   return strOut.toLowerCase();
	}

	MarblAmazonLink.prototype.htmlEncode = function(str) {
		return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	}

	MarblAmazonLink.prototype.htmlDecode = function(str) {
		return String(str).replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"');
	}

	MarblAmazonLink.prototype.S4 = function() {
		return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
	}

	MarblAmazonLink.prototype.createLink = function(strLinkRegions, strLabel, strFreetext, strAsin, strIsbn, iSize, iDisplayMode, strCustomImagePath) {
		var arrLinkRegions;
		if (strLinkRegions.toUpperCase() == 'ALL') {
			arrLinkRegions = this.AssociateIds[0];
		} else {
			arrLinkRegions = strLinkRegions.split(',');
		}

		iSize = parseInt(iSize);
		if (iSize === NaN || (iSize != 16 && iSize != 24 && iSize != 32 && iSize != 48)) {
			iSize = 16;
		}
		this.createBuyLink(arrLinkRegions, strLabel, strFreetext, strAsin, strIsbn, iSize, iDisplayMode, strCustomImagePath);
	}

	MarblAmazonLink.prototype.createDerivedLink = function(strLinkRegions, strLabel, strFreetext, iSize, iDisplayMode, strCustomImagePath) {
		var arrLinkRegions;
		if (strLinkRegions.toUpperCase() == 'ALL') {
			arrLinkRegions = this.AssociateIds[0];
		} else {
			arrLinkRegions = strLinkRegions.split(',');
		}

		iSize = parseInt(iSize);
		if (iSize === NaN || (iSize != 16 && iSize != 24 && iSize != 32 && iSize != 48)) {
			iSize = 16;
		}
		this.createDerivedBuyLink(arrLinkRegions, strLabel, strFreetext, iSize, iDisplayMode, strCustomImagePath);
	}

	/*
	 * This is the same as createLink, however will attempt to derive whether the freetext is a URL, ASIN, ISBN or search string
	 */
	MarblAmazonLink.prototype.createDerivedBuyLink = function(arrLinkRegions, strLabel, strFreetext, iSize, iDisplayMode, strCustomImagePath) {
		var iPos;
		var regExPattern;
		var p;
		// Process the search string
		strFreetext = strFreetext.trim();
		/*p = document.createElement('strong');		p.appendChild(document.createTextNode('Processing: ' + strFreetext));
		document.getElementById('leBody').appendChild(p);*/

		// Is it a URL?
		// https://www.amazon.co.uk/220-240V-Wireless-Transmitter-Household-Appliances/dp/B08SJM9VKR/ref=sr_1_110?dchild=1&keywords=wireless+240v+switch&qid=1613749448&sr=8-110
		// https://www.amazon.com/s?k=860+evo&ref=nb_sb_noss_1
		// https://www.amazon.com/s?k=860+evo
		regExPattern = /(http(s)?:\/\/)?([\w-]+\.)(amazon)+(\/[\/?%&=]*)?/;
		iPos = strFreetext.search(regExPattern);

		if (iPos > -1) {
			// Yes, it is a URL. The URL can contain data in a REST URL or as part of the QueryString, so we need to scan both after parsing
			// Remove http:// and https://
			strUrlString = strFreetext.replace('http://', '');
			strUrlString = strUrlString.replace('https://', '');
			// URL + (spaces) will have come in encoded as &#43;, so restore them back to " "
			strUrlString = strUrlString.replace(/&#43;/g, " ");

			// Does the URI have the tell-tale "/dp/" URL element, indicating that it is probalby a search friendly REST URL
			if (strUrlString.indexOf("/dp/") > -1) {
				arrFreetext = strUrlString.split('/');
				if (arrFreetext.length >= 3) {		// is it /<something>/dp/ ? Indicating that it is a search friendly URL?
					if (arrFreetext[2] == 'dp') {
						// Balance of probabiltiy says that is is a search friendly URL. 0 = www.amazon.xxx, 1 = search friendly product name, 2 = dp, 3 = ASIN
						if (strLabel == '' || strLabel == null) {	// If the calling API didn't specify a label, auto-populate it from the URL
							strLabel	= arrFreetext[1].replace(/\-/g, " ");
						}
						strAsin		= this.isAsin(arrFreetext[3]);
						if (strAsin != null) {
							if (this.PrintDebug) {
								console.log('URI /dp/: ' + strFreetext);
							}
							this.createBuyLink(arrLinkRegions, strLabel, '', strAsin, '', iSize, iDisplayMode, strCustomImagePath);
							return;
						}
					}	// Else Leave it to scan the string for ASIN/ISBN down below
				}
			} else if (strUrlString.indexOf("/ASIN/") > -1) { // Does the URI have the tell-tale (older) /ASIN/ indicating that the next elemet will be an ASIN
				arrFreetext = strUrlString.split('/');
				for (i = 0; i < arrFreetext.length; i++) {
					if (arrFreetext[i] == 'ASIN') {
						// The ASIN should be in the next URL index
						if ((i + 1) < arrFreetext.length) {
							strAsin		= this.isAsin(arrFreetext[(i + 1)]);
							if (strAsin != null) {
								if (this.PrintDebug) {
									console.log('URI /ASIN/: ' + strFreetext);
								}
								this.createBuyLink(arrLinkRegions, strLabel, '', strAsin, '', iSize, iDisplayMode, strCustomImagePath);
								return;
							}
						}
					}
				}
			} else {
				// Do we have keywords= or k= in the QueryString indicating a search phrase?
				regExPattern = /(\s)?(keywords=|k=)([a-zA-Z0-9\+ ]*)\&?/;
				iPos = strUrlString.search(regExPattern);
				if (iPos > -1) {	// Yes, use the URL's Amazon keyword search for the label provided that the calling API didn't pre-populate a label
					if (this.PrintDebug) {
						console.log('keyword querystring: ' + strFreetext);
					}
					if (strLabel == '' || strLabel == null) {
						strLabel = regExPattern.exec(strUrlString)[3];	// Group 1 = <full match> , Group 2 = keywords=/k=, Group 3 = keywords
						strLabel = strLabel.replace(/\+/g, " ");
					}
					this.createBuyLink(arrLinkRegions, strLabel, strLabel, '', '', iSize, iDisplayMode, strCustomImagePath);
					return;
				}		// No, attempt to search for an ASIN and a browser friendly URI string
			}
		}

		// Does it contain an ISBN? Do ISBN first because ASIN can look like ISBN
		strSearch = this.isIsbn(strFreetext);
		if (strSearch != null) {
			if (this.PrintDebug) {
				console.log('general ISBN: ' + strFreetext);
			}
			//strSearch = strFreetext.match(regExPattern);
			/*p = document.createElement('div');		p.appendChild(document.createTextNode('ISBN: ' + strSearch));
			document.getElementById('leBody').appendChild(p);*/
			if (strLabel == '' || strLabel == null) {
				strLabel = strSearch;
			}
			this.createBuyLink(arrLinkRegions, strLabel, '', '', strSearch, iSize, iDisplayMode, strCustomImagePath);
			return;
		}

		// Does it contain an ASIN?
		strSearch = this.isAsin(strFreetext);
		if (strSearch != null) {
			if (this.PrintDebug) {
				console.log('general ASIN: ' + strFreetext);
			}
			//strSearch = strFreetext.match(regExPattern);
			/*p = document.createElement('div');		p.appendChild(document.createTextNode('ASIN: ' + strSearch));
			document.getElementById('leBody').appendChild(p);*/
			if (strLabel == '' || strLabel == null) {
				strLabel = strSearch;
			}			
			this.createBuyLink(arrLinkRegions, strLabel, '', strSearch, '', iSize, iDisplayMode, strCustomImagePath);
			return;
		}

		// Fall back to something being better than nothing using the unedited caller freetext
		if (this.PrintDebug) {
			console.log('general fallback: ' + strFreetext);
		}
		this.createBuyLink(arrLinkRegions, strLabel, strFreetext, '', '', iSize, iDisplayMode, strCustomImagePath);
		return;
	}

	/*
	 * Attempts to derive whether a string is an ASIN. Returns the ASIN if true or null if not
	 */
	MarblAmazonLink.prototype.isAsin = function(strFreetext) {
		regExPattern = /([A-Z0-9]{10}?)/;
		iPos = strFreetext.search(regExPattern);
		if (iPos > -1) {
			return regExPattern.exec(strFreetext)[0];
		} else {
			return null;	
		}
	}

	/*
	 * Attempts to derive whether a string is an ISBN. Returns the ISBN if true or null if not
	 */
	MarblAmazonLink.prototype.isIsbn = function(strFreetext) {
		regExPattern = /^(978-?|979-?)?\d{1,5}-?\d{1,7}-?\d{1,6}-?\d{1,3}$/; //    /((978[\--– ])?[0-9][0-9\--– ]{10}[\--– ][0-9xX])|((978)?[0-9]{9}[0-9Xx])/;
		iPos = strFreetext.search(regExPattern);
		if (iPos > -1) {
			strIsbn = regExPattern.exec(strFreetext)[0];
			strIsbn = strIsbn.replace(/\ /g, '');
			strIsbn = strIsbn.replace(/\-/g, '');
			//alert(strIsbn);
			return strIsbn;
		} else {
			return null;	
		}
	}

	// iDisplayMode		1 = Icon Only, 2 = Text Only, 3 = Both. [Default = 1]
	MarblAmazonLink.prototype.createBuyLink = function(arrLinkRegions, strLabel, strFreetext, strAsin, strIsbn, iSize, iDisplayMode, strCustomImagePath) {
		// Decode the php parsed variables back to plain text [WARNING: variables are not XSS safe until re-sanitised]
		strLabel	= this.htmlDecode(strLabel);
		strFreetext	= this.htmlDecode(strFreetext);
		strAsin		= this.htmlDecode(strAsin);
		strIsbn		= this.htmlDecode(strIsbn);
		if (!strLabel) {
			if (strFreetext) {
				strLabel = strFreetext;
			} else if (strAsin) {
				strLabel = strAsin;
			} else if (strIsbn) {
				strLabel = strIsbn;
			} else {
				strLabel = 'Amazon Link';
			}
		}
	//	var scriptTag = document.getElementById(strContainerAfterId);
		var div = document.createElement('div');
			div.id					= this.createGuid() + '_widget';
			//div.style.marginLeft	= '4px';

		if (((iDisplayMode & this.IMAGE) == this.IMAGE)) {
			var img = document.createElement('img');
				if (strCustomImagePath === null || strCustomImagePath === undefined || strCustomImagePath === '') {
					img.src					= this.AssetsPath + 'images/amazon-' + iSize + 'x' + iSize + '.png';
					img.width				= iSize;
					img.height				= iSize;
				} else {
					img.src					= strCustomImagePath;
				}
				if (strLabel) {
					img.alt					= this.htmlEncode(strLabel);					
				} else {
					img.alt					= 'Amazon search dropdown menu';
				}
				img.className			= 'marbl_amazon_dropdown_icon';
			div.appendChild(img);
		}
		if (((iDisplayMode > this.IMAGE))) {
			var lbl = document.createElement('span');
				lbl.className			= 'marbl_amazon_dropdown_label';
				lbl.style.marginLeft	= '4px';
				if (strLabel != '') {
					lbl.appendChild(document.createTextNode(strLabel));
				} else {
					lbl.appendChild(document.createTextNode(strFreetext));
				}
			if (((iDisplayMode & this.IMAGE) == this.IMAGE)) {
				img.style.styleFloat	= 'left';
				img.style.cssFloat		= 'left';
			}
			div.appendChild(lbl);
		}

		if (this.RenderMode == this.INPLACE) {
			// Get the last executed script tag
			var scripts					= document.getElementsByTagName( 'script' );
			var scriptTag				= scripts[ scripts.length - 1 ];
				scriptTag.parentNode.appendChild(div);
		} else {
			var container				= document.getElementById(this.ContainerId);
			if (container != undefined) {
				while (container.firstChild) {						// Empty the container
					container.removeChild(container.firstChild);
				}
				container.appendChild(div);
			}
		}

		// Wire-up
		this.wireBuyLinks(div.id, arrLinkRegions, strFreetext, strAsin, strIsbn);

		// Increment the Issue Counter
		this.IssueCount++;
	}

	MarblAmazonLink.prototype.wireBuyLinks = function(containerId, arrLinkRegions, strFreetext, strAsin, strIsbn) {
		var elm = document.getElementById(containerId);
			elm.style.position	= 'relative';
			elm.style.display	= 'inline-block';
			elm.className		= 'marbl_amazon_dropdown_root';
			elm.onmouseover		= function() { document.getElementById(containerId + '_dropdown_content').style.display = 'block'; };
			elm.onmouseout		= function() { document.getElementById(containerId + '_dropdown_content').style.display = 'none'; };

		var container = document.createElement('div');
			container.id					= containerId + '_dropdown_content';
			container.style.display			= 'none';
			container.style.position		= 'absolute';
			container.style.minWidth		= '150px';
			//container.style.width			= '260px';
			container.style.backgroundColor	= '#f1f1f1';
			container.style.boxShadow		= '0px 8px 16px 0px rgba(0,0,0,0.2)';
			container.style.textAlign		= 'left';
			container.style.zIndex			= 1;
			container.className				= 'marbl_amazon_dropdown_container';

		strFreetext = strFreetext.replace('+', '%2b');
		strFreetext = strFreetext.replace(' ', '%20');
		//strSearchCode = encodeURI(strSearchCode);

		var iLinkCount = 0;
		var strLink;
		var strCountry;
		var amazon;
		var img;
		var div;
		var span;
		var disclaimer;
		
		if (this.DisclaimerText != null && this.DisclaimerText != '') {
			 disclaimer = document.createElement('div');
				disclaimer.classList.add('marbl_amazon_disclaimer');
				disclaimer.appendChild(document.createTextNode(this.DisclaimerText));
			if (this.DisclaimerPosition === this.TOP) {
				container.appendChild(disclaimer);
			}
		}
		if (arrLinkRegions.length > 0) {
			for (var i = 0; i < arrLinkRegions.length; i++) {
				strCountry = arrLinkRegions[i].substring(0,2).toUpperCase();
				strLink = this.getUrl(strCountry, strFreetext, strAsin, strIsbn);
				if (strLink != undefined) {
					amazon = document.createElement('a');
					amazon.style.display		= 'block';
					if (this.OpenInNewWindow) {
						amazon.target			= '_blank';
					}
					amazon.href					= strLink;
					amazon.className			= 'marbl_amazon_dropdown_row';
					if (this.LinksNoFollow) {
						amazon.setAttribute('rel', 'nofollow sponsored');
					}

					div = document.createElement('div');
					div.style.whiteSpace		= 'nowrap';
					div.style.padding			= '8px';

					if (this.DisplayFlags) {
						img = document.createElement('img');
						img.src					= this.AssetsPath + 'images/' + strCountry + '.gif';
						img.className			= 'marbl_amazon_dropdown_flagImage';
						img.style.width			= '16px';
						img.style.styleFloat	= 'left';
						img.style.cssFloat		= 'left';
						img.style.paddingTop	= '6px';
						img.style.marginRight	= '6px';
						img.alt					= strCountry + ' flag';
					}
				
					span = document.createElement('span');
					span.className				= 'marbl_amazon_dropdown_countryCode';
					span.appendChild(document.createTextNode(' (' + strCountry + ')'));
				
					if (this.DisplayFlags) {
						div.appendChild(img);
					}
					div.appendChild(document.createTextNode('Amazon'));
					div.appendChild(span);
				
					amazon.appendChild(div);

					container.appendChild(amazon);
					iLinkCount++;
				}
			}
		}
		if (typeof disclaimer === 'object' && this.DisclaimerPosition === this.BOTTOM) {
			container.appendChild(disclaimer);
		}

		if (iLinkCount > 0) {
			elm.appendChild(container);				// add the dropdown container to the parent
		} else {
			elm.parentNode.removeChild(elm);		// step back to the parent and remove it, displaying nothing what so ever
		}
	}


/*
 *
 *		EBAY
 *
 */
 function MarblEbayLink(strAssetsPath) {
	/* Enum */
	this.IMAGE						= 1;
	this.TEXT						= 2;
	this.BOTH						= 3;

	this.INPLACE					= 1;
	this.CONTAINER					= 2;

	this.TOP						= 1;
	this.BOTTOM						= 2;

	/* Fields */
	this.CampaignId					= null;
	this.Regions					= new Array(new Array(), new Array()); // [Country Code, eBay Country ID]
	this.RegionsCount				= 0;
	this.RegionsList				= ['AT','AU','BE','CA','CH','DE','ES','FR','GB','IE','IT','NL','PL','US'];

	this.IssueCount					= 0;	// How many codes this instance has created
	this.AssetsPath					= strAssetsPath;

	this.DisplayFlags				= true;
	this.OpenInNewWindow			= true;
	this.LinksNoFollow				= true;

	this.DisclaimerText				= null;
	this.DisclaimerPosition			= this.BOTTOM;

	this.RenderMode					= this.INPLACE
	this.ContainerId				= null;
	this.PrintDebug					= false;
 }
//	MarblEbayLink.prototype.synchronousGet = function(strTargetUrl)

	MarblEbayLink.prototype.addRegion = function(strCountry) {
		if ((strCountry.length == 2)) {
			this.Regions[0][this.RegionsCount] = strCountry.toUpperCase();
			this.Regions[1][this.RegionsCount] = this.getEpnCountryId(strCountry.toUpperCase());
			this.RegionsCount++;
		}
	}

	MarblEbayLink.prototype.addRegions = function(strCountryCsv) {
		if (strCountryCsv === 'ALL') {
			arrCountry = this.RegionsList;
		} else {
			arrCountry = strCountryCsv.split(',');
		}
		for (i = 0; i < arrCountry.length; i++) {
			this.addRegion(arrCountry[i].trim().toUpperCase());
		}
	}

	MarblEbayLink.prototype.setDisclaimer = function(iDisclaimerPosition, strDiscalimerText) {
		this.DisclaimerPosition		= iDisclaimerPosition;
		this.DisclaimerText			= strDiscalimerText;
	}

	MarblEbayLink.prototype.getEpnCountryId = function(strCountry) {
		if (this.RegionsCount > 0) {
			for (var i = 0; i < this.RegionsCount; i++) {
				if (this.Regions[0][i] == strCountry) {
					return this.Regions[1][i];
				}
			}
		}
		return '';
	}

	/*
	 *	icep_ff3= 1 == "Home Page" link
 	 *	icep_ff3= 2 == "Item ID" link
	 *	icep_ff3= 9 == "Search Results" link
 	 *	icep_ff3=11 == "Store" link
	 */
	MarblEbayLink.prototype.getUrl = function(strCountry, strFreetext, strItemId, strStoreId) {
		var strEpnCountryId = this.getEpnCountryId(strCountry);

		if (strEpnCountryId) {
			strFreetext = strFreetext.replace("\+", '%2b');
			strFreetext = strFreetext.replace(/\s/g, "%20");
			strFreetext = strFreetext.replace(/%20/g, "+");
			
			// Ebay specific change to allow a " character to be parsed into an affliate URL
			strFreetext = strFreetext.replace(/"/g, "%22");
		
			if (strFreetext != '') {			// Based on https://developer.ebay.com/api-docs/buy/static/ref-epn-link.html
				return this.getRedirectorUrl(strCountry) + '/sch/i.html?_nkw=' + encodeURI(strFreetext.replace('&', '%26')) + '&mkevt=1&mkcid=1&mkrid=' + strEpnCountryId + '&campid=' + this.CampaignId + '&toolid=10001';
			}
			if (strItemId != '') {
				return this.getRedirectorUrl(strCountry) + '/itm/' + encodeURI(strItemId) + '?&mkevt=1mkcid=1&mkrid=' + strEpnCountryId + '&campid=' + encodeURI(this.CampaignId) + '&toolid=10001';
			}
			if (strStoreId != '') {
				return this.getRedirectorUrl(strCountry) + '/str/' + encodeURI(strStoreId) + '?&mkevt=1mkcid=1&mkrid=' + strEpnCountryId + '&campid=' + encodeURI(this.CampaignId) + '&toolid=10001';
			}
		}
	}

	MarblEbayLink.prototype.getRedirectorUrl = function(strCountry) {
		switch (strCountry.toUpperCase()) {
			case 'AT':
				return 'https://www.ebay.at';
				break;
			case 'AU':
				return 'https://www.ebay.com.au';
				break;
			case 'BE':
				return 'https://www.ebay.be';
				break;
			case 'CA':
				return 'https://www.ebay.ca';
				break;
			case 'CH':
				return 'https://www.ebay.ch';
				break;
			case 'DE':
				return 'https://www.ebay.de';
				break;
			case 'ES':
				return 'https://www.ebay.es';
				break;
			case 'FR':
				return 'https://www.ebay.fr';
				break;
			case 'GB':
				return 'https://www.ebay.co.uk';
				break;
			case 'IE':
				return 'https://www.ebay.ie';
				break;
			case 'IT':
				return 'https://www.ebay.it';
				break;
			case 'NL':
				return 'https://www.ebay.nl';
				break;
			case 'PL':
				return 'https://www.ebay.pl';
				break;
			case 'US':
				return 'https://www.ebay.com';
				break;
			default:
				return null;
				break;
		}
	}

	MarblEbayLink.prototype.getEpnCountryId = function(strCountry) {
		switch (strCountry.toUpperCase()) {
			case 'AT':
				return '5221-53469-19255-0';
				break;
			case 'AU':
				return '705-53470-19255-0';
				break;
			case 'BE':
				return '1553-53471-19255-0';
				break;
			case 'CA':
				return '706-53473-19255-0';
				break;
			case 'CH':
				return '5222-53480-19255-0';
				break;
			case 'DE':
				return '707-53477-19255-0';
				break;
			case 'ES':
				return '1185-53479-19255-0';
				break;
			case 'FR':
				return '709-53476-19255-0';
				break;
			case 'IE':
				return '5282-53468-19255-0';
				break;
			case 'GB':
				return '710-53481-19255-0';
				break;
			case 'IT':
				return '724-53478-19255-0';
				break;
			case 'NL':
				return '1346-53482-19255-0';
				break;
			case 'PL':
				return '4908-226936-19255-0';
				break;
			case 'US':
				return '711-53200-19255-0';
				break;
			default:
				return null;
				break;
		}
	}

	MarblEbayLink.prototype.createGuid = function() {
	   strOut = this.S4() + this.S4() + '-' + this.S4() + '-4' + this.S4().substr(0,3) + '-' + this.S4() + '-' + this.S4() + this.S4() + this.S4();
	   return strOut.toLowerCase();
	}

	MarblEbayLink.prototype.htmlEncode = function(str) {
		return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	}

	MarblEbayLink.prototype.htmlDecode = function(str) {
		return String(str).replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"');
	}

	MarblEbayLink.prototype.S4 = function() {
		return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
	}

	MarblEbayLink.prototype.createLink = function(strStores, strLabel, strFreetext, strItemId, strStoreId, iSize, iDisplayMode, strCustomImagePath) {
		var arrRegions;
		if (strStores.toUpperCase() == 'ALL') {
			arrRegions = this.Regions[0];
		} else {
			arrRegions = strStores.split(',');
		}

		iSize = parseInt(iSize);
		if (iSize === NaN || (iSize != 16 && iSize != 24 && iSize != 32 && iSize != 48)) {
			iSize = 16;
		}
		this.createBuyLink(arrRegions, strLabel, strFreetext, strItemId, strStoreId, iSize, iDisplayMode, strCustomImagePath);
	}

	MarblEbayLink.prototype.createDerivedLink = function(strStores, strLabel, strFreetext, iSize, iDisplayMode, strCustomImagePath) {
		var arrRegions;
		if (strStores.toUpperCase() == 'ALL') {
			arrRegions = this.Regions[0];
		} else {
			arrRegions = strStores.split(',');
		}

		iSize = parseInt(iSize);
		if (iSize === NaN || (iSize != 16 && iSize != 24 && iSize != 32 && iSize != 48)) {
			iSize = 16;
		}
		this.createDerivedBuyLink(arrRegions, strLabel, strFreetext, iSize, iDisplayMode, strCustomImagePath);
	}

	/*
	 * This is the same as createLink, however will attempt to derive whether the freetext is a URL, Item ID or search string
	 * NOTE: Derived links Do NOT support Store ID links
	 */
	MarblEbayLink.prototype.createDerivedBuyLink = function(arrLinkRegions, strLabel, strFreetext, iSize, iDisplayMode, strCustomImagePath) {
		var iPos;
		var regExPattern;
		var p;
		// Process the search string
		strFreetext = strFreetext.trim();
		/*p = document.createElement('strong');		p.appendChild(document.createTextNode('Processing: ' + strFreetext));
		document.getElementById('leBody').appendChild(p);*/

		// Is it a URL?
		// https://www.ebay.co.uk/itm/New-Samsung-860-EVO-1TB-2TB-SSD-2-5-SATA-III-Solid-State-Drive-6Gb-s-V-NAND-MLC/254181125255?_trkparms=aid%3D111001%26algo%3DREC.SEED%26ao%3D1%26asc%3D20160908105057%26meid%3D047fa9a83d4a44e4b23efad92a664054%26pid%3D100675%26rk%3D2%26rkt%3D15%26mehot%3Dlo%26sd%3D164155063421%26itm%3D254181125255%26pmt%3D1%26noa%3D1%26pg%3D2380057%26brand%3DSamsung&_trksid=p2380057.c100675.m4236&_trkparms=pageci%3A3ae86f36-7393-11eb-acf2-3ec6f78159a0%7Cparentrq%3Ac021c69c1770aad30fb43e6effdabfea%7Ciid%3A1
		// https://www.ebay.co.uk/itm/250GB-500GB-1TB-2TB-Internal-SSD-Samsung-EVO-860-2-5/184080432546?hash=item2adc0c7da2:g:HaYAAOSwCXRcub-E
		// https://www.ebay.co.uk/sch/i.html?_from=R40&_trksid=m570.l1313&_nkw=860+evo&_sacat=0
		regExPattern = /(http(s)?:\/\/)?([\w-]+\.)(ebay)+(\/[\/?%&=]*)?/;
		iPos = strFreetext.search(regExPattern);
		if (iPos > -1) {
			// Yes, it is a URL. The URL can contain data in a REST URL or as part of the QueryString, so we need to scan both after parsing
			// Remove http:// and https://
			strUrlString = strFreetext.replace('http://', '');
			strUrlString = strUrlString.replace('https://', '');
			// URL + (spaces) will have come in encoded as &#43;, so restore them back to " "
			strUrlString = strUrlString.replace(/&#43;/g, " ");

			// Does the URI have the tell-tale "/itm/" URL element, indicating that it is probalby a search friendly REST URL
			if (strUrlString.indexOf("/itm/") > -1) {
				arrFreetext = strUrlString.split('/');
				if (arrFreetext.length >= 3) {		// [Length is Non-ZBI] is it /<something>/dp/ ? Indicating that it is a search friendly URL?
					if (arrFreetext[1] == 'itm') {
						// Balance of probabiltiy says that is is a search friendly URL. 0 = www.ebay.xxx, 1 = itm, 2 = search friendly product name, 3 = Item ID
						// However it can just be 0 = www.ebay.xxx, 1 = itm, 2 = Item ID
						if (this.isItemId(arrFreetext[2])) {
							strItemId		= this.isItemId(arrFreetext[2]);
							if (strLabel == '' || strLabel == null) {	// If the calling API didn't specify a label, auto-populate it from the URL
								strLabel	= strItemId;
							}
						} else {
							if (strLabel == '' || strLabel == null) {	// If the calling API didn't specify a label, auto-populate it from the URL
								strLabel	= arrFreetext[2].replace(/\-/g, " ");
							}
							if (arrFreetext.length > 3) {
								strItemId		= this.isItemId(arrFreetext[3]);		// ZBI element #4
							}
						}

						if (strItemId != null) {
							if (this.PrintDebug) {
								console.log('URI /itm/: ' + strFreetext);
							}
							this.createBuyLink(arrLinkRegions, strLabel, '', strItemId, '', iSize, iDisplayMode, strCustomImagePath);
							return;
						}
					}	// Else Leave it to scan the string for Item ID test down below
				}
			} else {
				// Do we have keywords= or k= in the QueryString indicating a search phrase?
				regExPattern = /(\s)?(_nkw=)([a-zA-Z0-9\+ ]*)\&?/;
				iPos = strUrlString.search(regExPattern);
				if (iPos > -1) {	// Yes, use the URL's Amazon keyword search for the label provided that the calling API didn't pre-populate a label
					if (strLabel == '' || strLabel == null) {
						strLabel = regExPattern.exec(strUrlString)[3];	// Group 1 = <full match> , Group 2 = keywords=/k=, Group 3 = keywords
						strLabel = strLabel.replace(/-/g, " ");
					}
					if (this.PrintDebug) {
						console.log('URI keyword in querystring: ' + strFreetext);
					}
					this.createBuyLink(arrLinkRegions, strLabel, strLabel, '', '', iSize, iDisplayMode, strCustomImagePath);
					return;
				}		// No, attempt to search for an ASIN and a browser friendly URI string
			}
		}
		
		// Is it an Item ID?
		strSearch = this.isItemId(strFreetext);
		if (strSearch != null) {
			//strSearch = strFreetext.match(regExPattern);
			/*p = document.createElement('div');		p.appendChild(document.createTextNode('ASIN: ' + strSearch));
			document.getElementById('leBody').appendChild(p);*/
			if (strLabel == '' || strLabel == null) {
				strLabel = strSearch;
			}
			if (this.PrintDebug) {
				console.log('general item ID: ' + strFreetext);
			}
			this.createBuyLink(arrLinkRegions, strLabel, '', strSearch, '', iSize, iDisplayMode, strCustomImagePath);
			return;
		}
		// Fall back to something being better than nothing using the unedited caller freetext
		if (this.PrintDebug) {
			console.log('general fallback: ' + strFreetext);
		}
		this.createBuyLink(arrLinkRegions, strLabel, strFreetext, '', '', iSize, iDisplayMode, strCustomImagePath);
		return;
	}

	/*
	 * Attempts to derive whether a string is an eBay Item ID. Returns the Item ID if true or null if not
	 */
	MarblEbayLink.prototype.isItemId = function(strFreetext) {
		regExPattern = /([0-9]{12}?)/;
		iPos = strFreetext.search(regExPattern);
		if (iPos > -1) {
			return regExPattern.exec(strFreetext)[0];
		} else {
			return null;	
		}
	}

	// Creates a 16x16 eBay buy link widget immediately after the named element (parm 1) using the keyword store search term (parm 2)
	// <div id="buyLink1"><img src="/homepage/includes/v3/images/ebay-16x16.png" width="16" height="16" alt="Buy on eBay" /></div>
	// iDisplayMode		1 = Icon Only, 2 = Text Only, 3 = Both. [Default = 1]
	MarblEbayLink.prototype.createBuyLink = function(arrRegions, strLabel, strFreetext, strItemId, strStoreId, iSize, iDisplayMode, strCustomImagePath) {
		// Decode the php parsed variables back to plain text [WARNING: variables are not XSS safe until re-sanitised]
		strLabel	= this.htmlDecode(strLabel);
		strFreetext	= this.htmlDecode(strFreetext);
		strItemId	= this.htmlDecode(strItemId);
		strStoreId	= this.htmlDecode(strStoreId);
	//	var scriptTag = document.getElementById(strContainerAfterId);
		var div = document.createElement('div');
			div.id					= this.createGuid() + '_widget';
			//div.style.marginLeft	= '4px';

		if (((iDisplayMode & this.IMAGE) == this.IMAGE)) {
			var img = document.createElement('img');
				if (strCustomImagePath === null || strCustomImagePath === undefined || strCustomImagePath === '') {
					img.src					= this.AssetsPath + 'images/ebay-' + iSize + 'x' + iSize + '.png';
					img.width				= iSize;
					img.height				= iSize;
				} else {
					img.src					= strCustomImagePath;
				}
				if (strLabel) {
					img.alt					= this.htmlEncode(strLabel);					
				} else {
					img.alt					= 'eBay search dropdown menu';
				}
				img.className			= 'marbl_ebay_dropdown_icon';
			div.appendChild(img);
		}
		if (((iDisplayMode > this.IMAGE))) {
			var lbl = document.createElement('span');
				lbl.className			= 'marbl_ebay_dropdown_label';
				lbl.style.marginLeft	= '4px';
				if (strLabel != '') {
					lbl.appendChild(document.createTextNode(strLabel));
				} else {
					lbl.appendChild(document.createTextNode(strFreetext));
				}
			if (((iDisplayMode & this.IMAGE) == this.IMAGE)) {
				img.style.styleFloat	= 'left';
				img.style.cssFloat		= 'left';
			}
			div.appendChild(lbl);
		}

		if (this.RenderMode == this.INPLACE) {
			// Get the last executed script tag
			var scripts					= document.getElementsByTagName( 'script' );
			var scriptTag				= scripts[ scripts.length - 1 ];
				scriptTag.parentNode.appendChild(div);
		} else {
			var container				= document.getElementById(this.ContainerId);
			if (container != undefined) {
				while (container.firstChild) {						// Empty the container
					container.removeChild(container.firstChild);
				}
				container.appendChild(div);
			}
		}

		// Wire-up
		this.wireBuyLinks(div.id, arrRegions, strFreetext, strItemId, strStoreId);
		
		// Increment the Issue Counter
		this.IssueCount++;
	}

	MarblEbayLink.prototype.wireBuyLinks = function(containerId, arrRegions, strFreetext, strItemId, strStoreId) {
		var elm = document.getElementById(containerId);
			elm.style.position	= 'relative';
			elm.style.display	= 'inline-block';
			elm.className		= 'marbl_ebay_dropdown_root';
			elm.onmouseover		= function() { document.getElementById(containerId + '_dropdown_content').style.display = 'block'; };
			elm.onmouseout		= function() { document.getElementById(containerId + '_dropdown_content').style.display = 'none'; };

		var container = document.createElement('div');
			container.id					= containerId + '_dropdown_content';
			container.style.display			= 'none';
			container.style.position		= 'absolute';
			container.style.minWidth		= '150px';
			//container.style.width			= '260px';
			container.style.backgroundColor	= '#f1f1f1';
			container.style.boxShadow		= '0px 8px 16px 0px rgba(0,0,0,0.2)';
			container.style.textAlign		= 'left';
			container.style.zIndex			= 1;
			container.className				= 'marbl_ebay_dropdown_container';

		strFreetext = strFreetext.replace('+', '%2b');
		strFreetext = strFreetext.replace(' ', '%20');
		//strSearchCode = encodeURI(strSearchCode);

		var iLinkCount = 0;
		var strLink;
		var strCountry;
		var ebay;
		var img;
		var div;
		var span;
		var disclaimer;

		if (this.DisclaimerText != null && this.DisclaimerText != '') {
			disclaimer = document.createElement('div');
				disclaimer.classList.add('marbl_ebay_disclaimer');
				disclaimer.appendChild(document.createTextNode(this.DisclaimerText));
			if (this.DisclaimerPosition === this.TOP) {
				container.appendChild(disclaimer);
			}
		}

		if (arrRegions.length > 0) {
			for (var i = 0; i < arrRegions.length; i++) {
				strCountry = arrRegions[i].substring(0,2).toUpperCase();
				strLink = this.getUrl(strCountry, strFreetext, strItemId, strStoreId);
				if (strLink != undefined) {
					ebay = document.createElement('a');
					ebay.style.display		= 'block';
					if (this.OpenInNewWindow) {
						ebay.target				= '_blank';
					}
					ebay.href					= strLink;
					ebay.className			= 'marbl_ebay_dropdown_row';
					if (this.LinksNoFollow) {
						ebay.setAttribute('rel', 'nofollow sponsored');
					}

					div = document.createElement('div');
					div.style.whiteSpace		= 'nowrap';
					div.style.padding			= '8px';

					if (this.DisplayFlags) {
						img = document.createElement('img');
						img.src					= this.AssetsPath + 'images/' + strCountry + '.gif';
						img.className			= 'marbl_ebay_dropdown_flagImage';
						img.style.width			= '16px';
						img.style.styleFloat	= 'left';
						img.style.cssFloat		= 'left';
						img.style.paddingTop	= '6px';
						img.style.marginRight	= '6px';
						img.alt					= strCountry + ' flag';
					}
				
					span = document.createElement('span');
					span.className				= 'marbl_ebay_dropdown_countryCode';
					span.appendChild(document.createTextNode(' (' + strCountry + ')'));
				
					if (this.DisplayFlags) {
						div.appendChild(img);
					}
					div.appendChild(document.createTextNode('eBay'));
					div.appendChild(span);
				
					ebay.appendChild(div);

					container.appendChild(ebay);
					iLinkCount++;
				}
			}
		}

		if (typeof disclaimer === 'object' && this.DisclaimerPosition === this.BOTTOM) {
			container.appendChild(disclaimer);
		}

		if (iLinkCount > 0) {
			elm.appendChild(container);				// add the dropdown container to the parent
		} else {
			elm.parentNode.removeChild(elm);		// step back to the parent and remove it, displaying nothing what so ever
		}
	}
