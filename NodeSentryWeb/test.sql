CREATE TABLE `sys_node_sentry` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '哨兵id',
  `sentry_token` char(32) NOT NULL DEFAULT '0' COMMENT '哨兵token',
  `sentry_ip` varchar(32) NOT NULL DEFAULT '' COMMENT '哨兵的ip',
  `server_ip` varchar(32) NOT NULL COMMENT '客户机ip',
  `config` text NOT NULL COMMENT '配置',
  `type` int(1) NOT NULL DEFAULT '0' COMMENT '类别 0为哨兵客户端 1为哨兵服务端',
  `open_state` int(1) NOT NULL DEFAULT '0' COMMENT '状态 1 运行 0 停止',
  `created_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `state` int(1) NOT NULL DEFAULT '1' COMMENT '状态 1 正常 0 禁用 -1删除',
  PRIMARY KEY (`id`),
  KEY	`sentry_token` (`sentry_token`)
) ENGINE=InnoDB AUTO_INCREMENT=47263 DEFAULT CHARSET=utf8