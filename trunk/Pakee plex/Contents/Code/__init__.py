import re

PLUGIN_TITLE = 'Pakee'
ART = 'art-default.jpg'
ICON = 'icon-default.png'
ICON_PREFS = 'icon-prefs.png'
BASE_URL = 'http://pakee.hopto.org/pakee/'
FEED_URL = BASE_URL + 'pakee-betaplus.xml'
PHOTOFEED_URL = BASE_URL + 'pakee-photography.xml'
MUSICFEED_URL = BASE_URL + 'fetchArtists-koolmuzone.php'
SEARCH_URL_PAKEE = BASE_URL + 'getYoutubePlaylistQuick.php?querydb=%s'
SEARCH_URL_YT = BASE_URL + 'getYoutubePlaylistQuick.php?queryyt=%s'
SEARCH_URL_YTUSER = BASE_URL + 'getYoutubePlaylistQuick.php?id=%s' 
SEARCH_URL_YTUSER_FAVS = BASE_URL + 'getYoutubePlaylistQuick.php?favorites=1&id=%s' 
YOUTUBE_VIDEO_PAGE = 'http://www.youtube.com/watch?v=%s'
YOUTUBE_VIDEO_FORMATS = ['Standard', 'Medium', 'High', '720p', '1080p']
YOUTUBE_FMT = [34, 18, 35, 22, 37]


####################################################################################################
def Start():
  Plugin.AddPrefixHandler('/video/Pakee', MainMenu, PLUGIN_TITLE, ICON, ART)
  Plugin.AddPrefixHandler('/photos/Pakee', MainMenuPhotos, PLUGIN_TITLE, ICON, ART)
  Plugin.AddPrefixHandler('/music/Pakee', MainMenuMusic, PLUGIN_TITLE, ICON, ART)
  Plugin.AddViewGroup('InfoList', viewMode='InfoList', mediaType='items')
  Plugin.AddViewGroup('List', viewMode='List', mediaType='items')
  Plugin.AddViewGroup("Images", viewMode="MediaPreview", mediaType="items")
  #Plugin.AddViewGroup("ImageStream", viewMode="Pictures", mediaType="items")
  #Plugin.AddViewGroup("Pictures", viewMode="ImageStream", contentType="photos")

  MediaContainer.title1 = PLUGIN_TITLE
  MediaContainer.viewGroup = 'InfoList'
  MediaContainer.art = R(ART)
  DirectoryItem.thumb = R(ICON)
  VideoItem.thumb = R(ICON)
  HTTP.CacheTime = CACHE_1HOUR
  HTTP.Headers['User-agent'] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:6.0) Gecko/20100101 Firefox/6.0'

##########################################################################################
def MainMenuMusic():
  dir = MediaContainer(viewGroup="InfoList", noCache=True)
  title = 'Pakee app'

  items = RSS.FeedFromURL(FEED_URL, errors='ignore', timeout=360)
  for item in items['items']:
    url = item.link
    title = item.title
    dir.Append(Function(DirectoryItem(ReadRSS,title),url=url))

  return dir


def MainMenuPhotos():
  dir = MediaContainer(viewGroup="InfoList", noCache=True)
  title = 'Pakee app'

  items = RSS.FeedFromURL(PHOTOFEED_URL, errors='ignore', timeout=360)
  for item in items['items']:
    url = item.link
    title = item.title
    dir.Append(Function(DirectoryItem(ReadRSS,title),url=url))

  return dir

###################################################################################################
def MainMenu():
  dir = MediaContainer(viewGroup="InfoList", noCache=True)
  title = 'Pakee app'

  items = RSS.FeedFromURL(FEED_URL, errors='ignore', timeout=360)
  for item in items['items']:
    url = item.link
    title = item.title
    dir.Append(Function(DirectoryItem(ReadRSS,title),url=url))

  dir.Append(Function(
    InputDirectoryItem(InputVideoList,
                       title="Search Pakee...",
                       prompt="Search Pakee...",
                       subtitle = 'Enter search terms \n\nSample searches include:\nhasb-e-haal\nimran khan\nzardari\naltaf\ndengue\nmango',
                       #thumb=R('icon-search.png'),
                       ),searchurl=SEARCH_URL_PAKEE))

  dir.Append(Function(
    InputDirectoryItem(InputVideoList,
                       title="Search YouTube...",
                       prompt="Search YouTube...",
                       subtitle = 'Enter YouTube search terms',
                       #thumb=R('icon-search.png'),
                       ),searchurl=SEARCH_URL_YT))

  dir.Append(Function(
    InputDirectoryItem(InputVideoList,
                       title="YouTube user uploads/playlists",
                       prompt="YouTube user uploads/playlists",
                       subtitle = 'Enter YouTube user whose uploads\n or playlists you wish to retrieve',
                       #thumb=R('icon-search.png'),
                       ),searchurl=SEARCH_URL_YTUSER))

  dir.Append(Function(
    InputDirectoryItem(InputVideoList,
                       title="YouTube user favorites",
                       prompt="YouTube user favorites",
                       subtitle = 'Enter YouTube user whose favorites\n you wish to retrieve',
                       #thumb=R('icon-search.png'),
                       ),searchurl=SEARCH_URL_YTUSER_FAVS))

  #dir.Append(Function(DirectoryItem(ReadRSS,title),url='http://pakee.hopto.org/pakee/pakee.xml'))
  #dir.Append(ReadRSS(dir,url='http://pakee.hopto.org/pakee/pakee.xml'))
  return dir

def InputVideoList(sender, query, searchurl):
  sender.itemTitle = 'Search: ' + query
  query = query.replace(' ', '+')
  url = searchurl % query
  dir = ReadRSS(sender, url)
  return dir

##################################################
def ReadRSS(sender,url):
  #Log('url is: ' + str(url))
  origurl = url
  summary = ''

  dir = MediaContainer(viewGroup="InfoList", noCache=True, title2=sender.itemTitle)
  items = RSS.FeedFromURL(url, errors='ignore')
  i=0
  for item in items['items']:

    i=i+1

    if (item.has_key('link')):
      url = item.link

    elif (item.has_key('links')):
      url = item.links[0]['href']

    if (item.has_key('media_thumbnail')):
      thumb = item.media_thumbnail[0]['url']
    elif (item.has_key('thumbnail')):
      thumb = item.thumbnail

    if (item.has_key('title')):
      title = item.title

    if (item.has_key('description')):
      summary = item.description    

    Log("url: "+str(url))

    #item is a video
    if 'youtube.com' in url or 'library/parts' in url:
      Log("Video found:"+str(url))
      if (i==1):
        Log(str(item))
      video_id = item.guid
      if (item.has_key('updated')):
        published = item.updated
      else:
        Log('pubDate not found!')
        published = ''
      
      
      if (item.has_key('media_starrating') and item.media_starrating['viewcount']):
        views = int(item.media_starrating['viewcount'])
      else:
        views = 0
      if (item.has_key('media_starrating') and item.media_starrating['average']):
        rating = float(item.media_starrating['average'])
      else:
        rating = 0
      if (item.has_key('media_content') and item.media_content[0]['duration']):
        length = int(item.media_content[0]['duration']) * 1000
      else:
        length = 0
      dir.Append(Function(VideoItem(PlayYTVideo, title, subtitle='Uploaded: ' + published + '     View count: ' + str(views),thumb=thumb,summary=summary, duration=length, rating=rating), video_id=video_id))

    elif '.mp3' in url or '.wma' in url or 'http://bit.ly' in url or '/getSharedFile/' in url:
      Log("audio file found: " + str(url))
      dir.Append(TrackItem(url, title = title, thumb = thumb, summary = summary))

      #dir.Append(Function(WebVideoItem(PlayMusicFile,title), url=url))

    elif url[-4:]=='.jpg' or url[-4:]=='.png' or url[-4:]=='.gif':
      Log("Image found: "+str(url))
      if i==1:
        dir = MediaContainer(viewGroup = 'Images')
      dir.Append(PhotoItem(url, title=title, summary=summary, thumb=thumb))
      #dir.Append(Function(DirectoryItem(HandlePhotos,title,thumb=thumb),url = origurl, title = title))
      #return PhotoItem(url.encode('utf-8','ignore'), title=title, summary=summary, thumb=thumb)


    #item is show/channel with thumb and description
    elif (item.has_key('description') and item.has_key('media_thumbnail') or item.has_key('thumbnail')):
      Log("Dir (desc and thumb) found: "+str(url))
      dir.Append(Function(DirectoryItem(ReadRSS,title,thumb=thumb,summary=summary),url=url))

    #item is show/channel with thumb 
    elif (item.has_key('media_thumbnail') or item.has_key('thumbnail')):
      Log("Dir (thumb) found: "+str(url))
      dir.Append(Function(DirectoryItem(ReadRSS,title,thumb=thumb),url=url))


    #item is a top level item with only title and url
    else:
      Log("top level item found")
      dir.Append(Function(DirectoryItem(ReadRSS,title),url=url))

  return dir



################################################################
def HandlePhotos(sender, url = "", title = ''):
  dir = MediaContainer(viewGroup = "ImageStream")
  items = RSS.FeedFromURL(url, errors='ignore')

  for item in items['items']:

    if (item.has_key('link')):
      src = item.link
    elif (item.has_key('links')):
      src = item.links[0]['href']

    if (item.has_key('media_thumbnail')):
      thumb = item.media_thumbnail[0]['url']

    if (item.has_key('title')):
      title = item.title

    if (item.has_key('description')):
      summary = item.description    

    if src is None:
      Log(item)
    else:
      Log("adding photo: " + str(src))
      dir.Append(PhotoItem(src, title, summary, thumb))
    
  return dir

##################################################
def PlayMusicFile(sender,url):
  return TrackItem(url,'','','')
  #oc = ObjectContainer(view_group = "InfoList")
  #return PartObject(key = WebVideoURL(url))
  #oc.add(TrackObject(url = url, key=url, rating_key="0"))
  #oc.add(WebVideoURL(url))
  #return oc



##################################################
def PlayYTVideo(sender,video_id):
  yt_page = HTTP.Request(YOUTUBE_VIDEO_PAGE % (video_id), cacheTime=1).content
    
  fmt_url_map = re.findall('"url_encoded_fmt_stream_map".+?"([^"]+)', yt_page)[0]
  fmt_url_map = fmt_url_map.replace('\/', '/').split(',')

  fmts = []
  fmts_info = {}

  for f in fmt_url_map:
    map = {}
    params = f.split('\u0026')
    for p in params:
      (name, value) = p.split('=')
      map[name] = value
    quality = str(map['itag'])
    fmts_info[quality] = String.Unquote(map['url'])
    fmts.append(quality)

  #index = YOUTUBE_VIDEO_FORMATS.index(Prefs['youtube_fmt'])
  index = 3
  if YOUTUBE_FMT[index] in fmts:
    fmt = YOUTUBE_FMT[index]
  else:
    for i in reversed( range(0, index+1) ):
      if str(YOUTUBE_FMT[i]) in fmts:
        fmt = YOUTUBE_FMT[i]
        break
      else:
        fmt = 5

  url = (fmts_info[str(fmt)]).decode('unicode_escape')
  Log("  VIDEO URL --> " + url)
  return Redirect(url)

