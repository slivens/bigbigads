// supported languages
var settings = {
    timestamp: Date.parse(new Date()),
    layout: {
        pageSidebarClosed: false, // sidebar menu state
        pageContentWhite: true, // set page content layout
        pageBodySolid: false, // solid body color state
        pageAutoScrollOnLoad: 1000 // auto scroll to top on page load
    },
    assetsPath: '../assets',
    globalPath: '../assets/global',
    layoutPath: '../assets/layouts/layout3',
    baseurl: '/app/',
    remoteurl: '', // 根目录
    imgRemoteBase: [
        'http://image1.bigbigads.com:88',
        'http://image2.bigbigads.com:88',
        'http://image3.bigbigads.com:88',
        'http://image4.bigbigads.com:88'
    ],
    videoRemoteBase: 'http://image1.bigbigads.com:88',
    searchSetting: {
        pageCount: 10, // 每一页的数据量
        durationRange: [0, 180],
        seeTimesRange: [0, 180],
        orderBy: [{
            key: 'last_view_date',
            value: 'Last_Seen',
            last: false,
            group: 'time',
            permission: 'date_sort'
        }, {
            key: 'duration_days',
            value: 'Duration',
            last: true,
            group: 'time',
            permission: 'duration_sort'
        }, {
            key: 'engagements',
            value: 'Engagements',
            last: false,
            group: 'seen',
            permission: 'engagements_sort'
        }, {
            key: 'views',
            value: 'Video Views',
            last: false,
            group: 'seen',
            permission: 'views_sort'
        }, {
            key: 'engagements_per_7d',
            value: 'Engagements Growth',
            last: false,
            group: 'seen',
            permission: 'engagement_inc_sort'
        }, {
            key: 'views_per_7d',
            value: 'Video Views Growth',
            last: true,
            group: 'seen',
            permission: 'views_inc_sort'
        }, {
            key: 'likes',
            value: 'Likes',
            last: false,
            group: 'interactive',
            permission: 'likes_sort'
        }, {
            key: 'shares',
            value: 'Shares',
            last: false,
            group: 'interactive',
            permission: 'shares_sort'
        }, {
            key: 'comments',
            value: 'Comments',
            last: false,
            group: 'interactive',
            permission: 'comment_sort'
        }, {
            key: 'likes_per_7d',
            value: 'Likes Growth',
            last: false,
            group: 'interactive',
            permission: 'likes_inc_sort'
        }, {
            key: 'shares_per_7d',
            value: 'Shares Growth',
            last: false,
            group: 'interactive',
            permission: 'shares_inc_sort'
        }, {
            key: 'comments_per_7d',
            value: 'Comments Growth',
            last: true,
            group: 'interactive',
            permission: 'comments_inc_sort'
        }],
        adsTypes: [{
            key: 'timeline',
            value: 'Newsfeed',
            permission: 'timeline_filter'
        }, {
            key: 'rightcolumn',
            value: 'Right Column',
            permission: 'rightcolumn_filter'
        }, {
            key: 'phone',
            value: 'Mobile',
            permission: 'phone_filter'
        }, {
            key: 'suggested app',
            value: 'App',
            permission: 'app_filter'
        }],
        categoryList: [{
            key: "Advertising Agency",
            value: "Advertising Agency"
        }, {
            key: "Agriculture Company",
            value: "Agriculture Company"
        }, {
            key: "App Page",
            value: "App Page"
        }, {
            key: "Arts & Entertainment",
            value: "Arts & Entertainment"
        }, {
            key: "Author/Writer",
            value: "Author/Writer"
        }, {
            key: "Baby Goods/Kids Goods",
            value: "Baby Goods/Kids Goods"
        }, {
            key: "Bags/Luggage",
            value: "Bags/Luggage"
        }, {
            key: "Bar",
            value: "Bar"
        }, {
            key: "Book",
            value: "Book"
        }, {
            key: "Brand",
            value: "Brand"
        }, {
            key: "Business Service",
            value: "Business Service"
        }, {
            key: "Business/Economy",
            value: "Business/Economy"
        }, {
            key: "Car",
            value: "Car"
        }, {
            key: "Cargo & Freight Company",
            value: "Cargo & Freight Company"
        }, {
            key: "Cause",
            value: "Cause"
        }, {
            key: "Cleaning Service",
            value: "Cleaning Service"
        }, {
            key: "Clothing",
            value: "Clothing"
        }, {
            key: "Comedian",
            value: "Comedian"
        }, {
            key: "Community",
            value: "Community"
        }, {
            key: "Company/Internet Company",
            value: "Company/Internet Company"
        }, {
            key: "Consulting Agency",
            value: "Consulting Agency"
        }, {
            key: "Contractor",
            value: "Contractor"
        }, {
            key: "Dentist",
            value: "Dentist"
        }, {
            key: "Doctor",
            value: "Doctor"
        }, {
            key: "Education",
            value: "Education"
        }, {
            key: "Electronics",
            value: "Electronics"
        }, {
            key: "Energy",
            value: "Energy"
        }, {
            key: "Entrepreneur",
            value: "Entrepreneur"
        }, {
            key: "Event",
            value: "Event"
        }, {
            key: "Event Planning Service",
            value: "Event Planning Service"
        }, {
            key: "Fictional Character",
            value: "Fictional Character"
        }, {
            key: "Financial Service",
            value: "Financial Service"
        }, {
            key: "Food",
            value: "Food"
        }, {
            key: "Games/Toys",
            value: "Games/Toys"
        }, {
            key: "Gift",
            value: "Gift"
        }, {
            key: "Hair Salon",
            value: "Hair Salon"
        }, {
            key: "Health/Beauty",
            value: "Health/Beauty"
        }, {
            key: "Heating, Ventilating & Air Conditioning Service",
            value: "Heating, Ventilating & Air Conditioning Service"
        }, {
            key: "Home Decor",
            value: "Home Decor"
        }, {
            key: "Industrial Company",
            value: "Industrial Company"
        }, {
            key: "Insurance Agent",
            value: "Insurance Agent"
        }, {
            key: "Jewelry/Watches",
            value: "Jewelry/Watches"
        }, {
            key: "Just For Fun",
            value: "Just For Fun"
        }, {
            key: "Kitchen/Cooking",
            value: "Kitchen/Cooking"
        }, {
            key: "Landscape Company",
            value: "Landscape Company"
        }, {
            key: "Law Firm",
            value: "Law Firm"
        }, {
            key: "Magazine",
            value: "Magazine"
        }, {
            key: "Media/News",
            value: "Media/News"
        }, {
            key: "Medical & Health",
            value: "Medical & Health"
        }, {
            key: "Mobile Phone Shop",
            value: "Mobile Phone Shop"
        }, {
            key: "Movie",
            value: "Movie"
        }, {
            key: "Musician/Band",
            value: "Musician/Band"
        }, {
            key: "Non-Governmental Organization (NGO)",
            value: "Non-Governmental Organization (NGO)"
        }, {
            key: "Non-Profit Organization",
            value: "Non-Profit Organization"
        }, {
            key: "Organization",
            value: "Organization"
        }, {
            key: "Others",
            value: "Others"
        }, {
            key: "Personal Blog",
            value: "Personal Blog"
        }, {
            key: "Pet Service",
            value: "Pet Service"
        }, {
            key: "Photographer",
            value: "Photographer"
        }, {
            key: "Politician",
            value: "Politician"
        }, {
            key: "Producer",
            value: "Producer"
        }, {
            key: "Product/Service",
            value: "Product/Service"
        }, {
            key: "Public Figure",
            value: "Public Figure"
        }, {
            key: "Publisher",
            value: "Publisher"
        }, {
            key: "Real Estate",
            value: "Real Estate"
        }, {
            key: "Record Label",
            value: "Record Label"
        }, {
            key: "Recreation & Fitness",
            value: "Recreation & Fitness"
        }, {
            key: "Religious Organization",
            value: "Religious Organization"
        }, {
            key: "Restaurant/Cafe/Hotel",
            value: "Restaurant/Cafe/Hotel"
        }, {
            key: "Shopping/Retail",
            value: "Shopping/Retail"
        }, {
            key: "Society/Culture Website",
            value: "Society/Culture Website"
        }, {
            key: "Software",
            value: "Software"
        }, {
            key: "Sports",
            value: "Sports"
        }, {
            key: "Tattoo & Piercing Shop",
            value: "Tattoo & Piercing Shop"
        }, {
            key: "Tools/Equipment",
            value: "Tools/Equipment"
        }, {
            key: "Travel",
            value: "Travel"
        }, {
            key: "TV Show/TV Network",
            value: "TV Show/TV Network"
        }, {
            key: "Website/Entertainment Website",
            value: "Website/Entertainment Website"
        }, {
            key: "Wine/Spirits",
            value: "Wine/Spirits"
        }],
        formatList: [{
            key: "SingleVideo",
            value: "Video"
        }, {
            key: "Canvas",
            value: "Others"
        }, {
            key: "SingleImage",
            value: "Image"
        }, {
            key: "Carousel",
            value: "Carousel"
        }],
        buttondescList: [{
            key: "Apply Now",
            value: "Apply Now"
        }, {
            key: "Book Now",
            value: "Book Now"
        }, {
            key: "Buy",
            value: "Buy"
        }, {
            key: "Buy Now",
            value: "Buy Now"
        }, {
            key: "Buy Tickets",
            value: "Buy Tickets"
        }, {
            key: "Call Now",
            value: "Call Now"
        }, {
            key: "Contact Us",
            value: "Contact Us"
        }, {
            key: "Donate",
            value: "Donate"
        }, {
            key: "Donate Now",
            value: "Donate Now"
        }, {
            key: "Download",
            value: "Download"
        }, {
            key: "Get Deal",
            value: "Get Deal"
        }, {
            key: "Get Directions",
            value: "Get Directions"
        }, {
            key: "Get Offer",
            value: "Get Offer"
        }, {
            key: "Get Quote",
            value: "Get Quote"
        }, {
            key: "Get Tickets",
            value: "Get Tickets"
        }, {
            key: "Get Your Code",
            value: "Get Your Code"
        }, {
            key: "Install App",
            value: "Install App"
        }, {
            key: "Install Now",
            value: "Install Now"
        }, {
            key: "Learn More",
            value: "Learn More"
        }, {
            key: "Like Page",
            value: "Like Page"
        }, {
            key: "Liked",
            value: "Liked"
        }, {
            key: "Listen Now",
            value: "Listen Now"
        }, {
            key: "Listen on Apple Music",
            value: "Listen on Apple Music"
        }, {
            key: "Listen on Deezer",
            value: "Listen on Deezer"
        }, {
            key: "Listen on Whooshkaa",
            value: "Listen on Whooshkaa"
        }, {
            key: "Open Link",
            value: "Open Link"
        }, {
            key: "Order Now",
            value: "Order Now"
        }, {
            key: "Play",
            value: "Play"
        }, {
            key: "Play Game",
            value: "Play Game"
        }, {
            key: "Play Now",
            value: "Play Now"
        }, {
            key: "Request Time",
            value: "Request Time"
        }, {
            key: "Save",
            value: "Save"
        }, {
            key: "Save Offer",
            value: "Save Offer"
        }, {
            key: "SaveSaved",
            value: "SaveSaved"
        }, {
            key: "See Details",
            value: "See Details"
        }, {
            key: "See Menu",
            value: "See Menu"
        }, {
            key: "Sell Now",
            value: "Sell Now"
        }, {
            key: "Send Message",
            value: "Send Message"
        }, {
            key: "Shop Now",
            value: "Shop Now"
        }, {
            key: "Sign Up",
            value: "Sign Up"
        }, {
            key: "Spotify Icon",
            value: "Spotify Icon"
        }, {
            key: "Spotify IconAdd to Spotify",
            value: "Spotify IconAdd to Spotify"
        }, {
            key: "Use App",
            value: "Use App"
        }, {
            key: "Use Now",
            value: "Use Now"
        }, {
            key: "View Event",
            value: "View Event"
        }, {
            key: "Visit Website",
            value: "Visit Website"
        }, {
            key: "Vote Now",
            value: "Vote Now"
        }, {
            key: "Watch More",
            value: "Watch More"
        }, {
            key: "Watch Video",
            value: "Watch Video"
        }],
        rangeList: [{
            key: "adser_name,adser_username",
            value: "Advertiser",
            permission: "advertiser_search"
        }, {
            key: "link,buttonlink,dest_site",
            value: "URL",
            permission: "dest_site_search"
        }, {
            key: "description,name,caption,message",
            value: "Advertisement",
            permission: "content_search"
        } /* { 摒弃，已细化出为更具体的受众过滤
            key:"whyseeads,whyseeads_all",
            value:"Audience",
            permission:"audience_search"
        } */],
        langList: [{
            key: "English",
            value: "English"
        }, {
            key: "Chinese",
            value: "Chinese"
        }, {
            key: "Japanese",
            value: "Japanese"
        }, {
            key: "Korean",
            value: "Korean"
        }, {
            key: "French",
            value: "French"
        }, {
            key: "German",
            value: "German"
        }, {
            key: "Portuguese",
            value: "Portuguese"
        }, {
            key: "Spanish",
            value: "Spanish"
        }, {
            key: "Russian",
            value: "Russian"
        }, {
            key: "Arabic",
            value: "Arabic"
        }, {
            key: "Others",
            value: "Others"
        }],
        /* add country list */
        country: [
            {
                key: "Brazil",
                value: "Brazil"
            }, {
                key: "Canada",
                value: "Canada"
            }, {
                key: "Denmark",
                value: "Denmark"
            }, {
                key: "Finland",
                value: "Finland"
            }, {
                key: "France",
                value: "France"
            }, {
                key: "Germany",
                value: "Germany"
            }, {
                key: "Hongkong",
                value: "Hongkong"
            }, {
                key: "Indonesia",
                value: "Indonesia"
            }, {
                key: "India",
                value: "India"
            }, {
                key: "Italy",
                value: "Italy"
            }, {
                key: "Japan",
                value: "Japan"
            }, {
                key: "Korea",
                value: "Korea"
            }, {
                key: "Mexico",
                value: "Mexico"
            }, {
                key: "Norway",
                value: "Norway"
            }, {
                key: "Philippines",
                value: "Philippines"
            }, {
                key: "Russia",
                value: "Russia"
            }, {
                key: "Sweden",
                value: "Sweden"
            }, {
                key: "Thailand",
                value: "Thailand"
            }, {
                key: "United Kingdom",
                value: "United Kingdom"
            }, {
                key: "United States",
                value: "United States"
            }, {
                key: "Vietnam",
                value: "Vietnam"
            }, {
                key: "Taiwan",
                value: "Taiwan"
            }],
        trackingList: [{
            key: "CPV Lab",
            value: "CPV Lab"
        }, {
            key: "iMobiTrax",
            value: "iMobiTrax"
        }, {
            key: "prosper202",
            value: "prosper202"
        }, {
            key: "voluum",
            value: "voluum"
        }, {
            key: "thrive",
            value: "thrive"
        }, {
            key: "google analytics",
            value: "google analytics"
        }],
        affiliateList: [{
            key: "oasis",
            value: "oasis"
        }, {
            key: "ad4game",
            value: "ad4game"
        }],
        eCommerceList: [{
            key: "teespring",
            value: "teespring"
        }, {
            key: "teechip",
            value: "teechip"
        }, {
            key: "teezily",
            value: "teezily"
        }, {
            key: "shopify",
            value: "shopify"
        }, {
            key: "magento",
            value: "magento"
        }, {
            key: "wooCommerce",
            value: "wooCommerce"
        }],
        audienceAge: [{
            key: "18-24",
            value: "18-24"
        }, {
            key: "25-34",
            value: "25-34"
        }, {
            key: "35-44",
            value: "35-44"
        }, {
            key: "45-54",
            value: "45-54"
        }, {
            key: "55-64",
            value: "55-64"
        }, {
            key: "65",
            value: "65"
        }],
        audienceGender: [{
            key: "only female",
            value: "only female"
        }, {
            key: "only male",
            value: "only male"
        }, {
            key: "both",
            value: "both"
        }, {
            key: "include female",
            value: "include female"
        }, {
            key: "include male",
            value: "include male"
        }],
        objective: [{
            key: "APP_INSTALLS",
            value: "APP_INSTALLS"
        }, {
            key: "BRAND_AWARENESS",
            value: "BRAND_AWARENESS"
        }, {
            key: "CANVAS_APP_INSTALLS",
            value: "CANVAS_APP_INSTALLS"
        }, {
            key: "EVENT_RESPONSES",
            value: "EVENT_RESPONSES"
        }, {
            key: "LEAD_GENERATION",
            value: "LEAD_GENERATION"
        }, {
            key: "LINK_CLICKS",
            value: "LINK_CLICKS"
        }, {
            key: "LOCAL_AWARENESS",
            value: "LOCAL_AWARENESS"
        }, {
            key: "PAGE_LIKES",
            value: "PAGE_LIKES"
        }, {
            key: "POST_ENGAGEMENT",
            value: "POST_ENGAGEMENT"
        }, {
            key: "PRODUCT_CATALOG_SALES",
            value: "PRODUCT_CATALOG_SALES"
        }, {
            key: "REACH",
            value: "REACH"
        }, {
            key: "STORE_VISITS",
            value: "STORE_VISITS"
        }, {
            key: "VIDEO_VIEWS",
            value: "VIDEO_VIEWS"
        }, {
            key: "WEBSITE_CONVERSIONS",
            value: "WEBSITE_CONVERSIONS"
        }]
    }
}

export default settings
