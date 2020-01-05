# Youtube scrapper
For developement Laravel framework is choosen. For Youtube access https://github.com/alaouy/Youtube library is choosen. Put your Youtube api key and channel ID into .env file.
## Data structure
Table of `channels` contains:
- `channel_id`,
- `rating` - all channel videos views / N time median rating.
Table of `videos` identified by `ChannelID` contains:
- `channel_id`,
- `video_id`,
- `tags` - string of comma separated tags,
- `cycle_no` - scan cycle no,
- `view_count` - views count,
- `rating` - views / N rating.

## Business logics
As an MVC structure two classes hold majority of the work: app/services/cycles.php and app/services/builder.php. Builder is just a simplified version of the pattern.
For scraping channels cronjob is needed every 60 minutes for command `php artisan scrap:channel`.
