CREATE TABLE `login`
(
    `id`       bigint      NOT NULL AUTO_INCREMENT,
    `flag_one` varchar(50) NOT NULL DEFAULT '' COMMENT '登录标识1:type=1是账号名,type=2是手机号,type=3是邮箱,type=4是三方登录唯一标识',
    `flag_two` varchar(50) NOT NULL DEFAULT '' COMMENT '登录标识2:type=4是渠道',
    `type`     tinyint     NOT NULL DEFAULT '1' COMMENT '登录类型:1-普通,2-手机号,3-邮箱,4-三方登录',
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_type_flag` (`type`,`flag_one`,`flag_two`),
    KEY        `idx_id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='登录'

CREATE TABLE `user`
(
    `id`        bigint       NOT NULL AUTO_INCREMENT,
    `user_id`   bigint       NOT NULL DEFAULT '0' COMMENT '用户id',
    `user_name` varchar(50)  NOT NULL DEFAULT '' COMMENT '英文名',
    `zh`        varchar(15)  NOT NULL DEFAULT '' COMMENT '中文名',
    `phone`     varchar(20)  NOT NULL DEFAULT '' COMMENT '手机号',
    `id_card`   varchar(30)  NOT NULL DEFAULT '' COMMENT '身份证',
    `email`     varchar(256) NOT NULL DEFAULT '' COMMENT '邮箱',
    `create_ip` varchar(50)  NOT NULL DEFAULT '' COMMENT '创建IP',
    `create_at` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增时间',
    PRIMARY KEY (`id`, `create_at`),
    PARTITION BY RANGE (unix_timestamp(`create_at`))
        (PARTITION create_at2021 VALUES LESS THAN (1609459200) ENGINE = InnoDB,
        PARTITION create_at2022 VALUES LESS THAN (1640995200) ENGINE = InnoDB,
        PARTITION create_at2023 VALUES LESS THAN (1672531200) ENGINE = InnoDB,
        PARTITION create_at2024 VALUES LESS THAN (1704067200) ENGINE = InnoDB)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='用户维度表'

CREATE TABLE `user_cms`
(
    `id`        bigint       NOT NULL AUTO_INCREMENT COMMENT '用户id',
    `name`      varchar(50)  NOT NULL DEFAULT '' COMMENT '英文名',
    `zh`        varchar(15)  NOT NULL DEFAULT '' COMMENT '中文名',
    `phone`     varchar(20)  NOT NULL DEFAULT '' COMMENT '手机号',
    `id_card`   varchar(30)  NOT NULL DEFAULT '' COMMENT '身份证',
    `email`     varchar(256) NOT NULL DEFAULT '' COMMENT '邮箱',
    `create_ip` varchar(50)  NOT NULL DEFAULT '' COMMENT '创建IP',
    `create_at` timestamp    NOT NULL DEFAULT (now()) COMMENT '新增时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='后台用户表'

CREATE TABLE `user_role`
(
    `user_id` bigint NOT NULL,
    `role_id` bigint NOT NULL,
    PRIMARY KEY (`user_id`, `role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='用户角色表'

CREATE TABLE `role`
(
    `id`        bigint      NOT NULL AUTO_INCREMENT,
    `name`      varchar(50) NOT NULL DEFAULT '' COMMENT '角色名',
    `create_by` varchar(50) NOT NULL DEFAULT '' COMMENT '创建用户',
    `update_by` varchar(50) NOT NULL DEFAULT '' COMMENT '更新用户',
    `create_at` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_at` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci

CREATE TABLE `role_permission`
(
    `role_id`         bigint      NOT NULL DEFAULT '0',
    `permission_id`   bigint      NOT NULL DEFAULT '0',
    `permission_type` varchar(10) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='角色权限'

CREATE TABLE `permission_menu`
(
    `id`        bigint      NOT NULL AUTO_INCREMENT,
    `pid`       bigint      NOT NULL DEFAULT '0',
    `name`      varchar(50) NOT NULL DEFAULT '',
    `path`      varchar(15) NOT NULL DEFAULT '',
    `level`     tinyint     NOT NULL DEFAULT '0',
    `create_at` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `update_at` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `create_by` varchar(50) NOT NULL DEFAULT '',
    `update_by` varchar(50) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci

