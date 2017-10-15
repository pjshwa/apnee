CREATE TABLE IF NOT EXISTS `crema_widget_contents` (
  `id` int(6) unsigned NOT NULL,
  `content` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=euckr;

ALTER TABLE `crema_widget_contents`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `crema_widget_contents`
  MODIFY `id` int(6) unsigned NOT NULL AUTO_INCREMENT;
