<?php
class RssFeedController extends _Controller {
    public function index() {
        $category = paramFromGet("category");
        $search_string = paramFromGet("search_string");

        $sql = "SELECT l.*
                FROM listing l
                WHERE l.listing_status = 'available'
                AND l.listing_type = 'free'";
        $sql .= empty($search_string) ? '' : " AND match(title,description) against ( " . quoteSQL($search_string) . ")";

        $sql .= " ORDER BY listing_date DESC";
        $listings = runQueryGetAll($sql);

        $channel_title = "- The site where everything is free";
        $channel_title .= empty($category) ? '' : "- " . $category;
        $channel_title .= empty($search_string) ? '' : " keyword:" . $search_string;

        header("Content-Type: application/xml; charset=ISO-8859-1");
        echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>";
        ?>
        <rss version="2.0" xmlns:atom="//www.w3.org/2005/Atom">
            <channel>
                <atom:link href="<?=(APP_URL)?>rss_feed" rel="self" type="application/rss+xml"/>
                <title>Freestuff <?=($channel_title)?></title>
                <link>http://www.freestuff.co.nz/</link>
                <description>The site where everything is genuinely Free</description>
                <language>EN</language>
                <image>
                    <title>Freestuff <?=($channel_title)?></title>
                    <url>https://www.freestuff.co.nz/img/rss_logo.png</url>
                    <link>https://www.freestuff.co.nz/</link>
                    <width>140</width>
                    <height>34</height>
                </image>
                <? foreach ($listings as $listing) {
                    $tmp_url = 'https://www.freestuff.co.nz/' . seoFriendlyURLs($listing["listing_id"], "listing", FALSE, $listing["title"]);
                    ?>
                    <item>
                        <guid><?=($tmp_url)?></guid>
                        <title><![CDATA[<?=($listing["title"])?>]]></title>
                        <link><?=($tmp_url)?></link>
                        <description><![CDATA[<?=($listing["description"])?>]]></description>
                    </item>
                <? } ?>
            </channel>
        </rss>
        <?
        die();
    }
}
