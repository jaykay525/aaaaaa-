<?php
return [
	'menu' => [
		[
			'title' => '通用',
			'url'   => 'Common/index',
			'ico'   => 'home',
			'show'  => 'no',
			'list'  => [
				['title' => '通用模块', 'url' => 'Common/index', 'list' => [
					['title' => '上传功能', 'url' => 'File/upload', 'filter_list' => [
						['title' => '上传文件', 'url' => 'File/upload'],
						['title' => '本地上传', 'url' => 'File/win_upload'],
						['title' => '上传在线管理', 'url' => 'File/win_online'],
						['title' => '编辑器上传', 'url' => 'File/ueditor_uploads'],
					]],
					['title' => '弹窗选择', 'url' => 'Company/win_select_company', 'filter_list' => [
						['title' => '选择城市', 'url' => 'Area/win_select_city'],
						['title' => '选择订单', 'url' => 'Order/win_select_order'],
						['title' => '选择用户', 'url' => 'User/win_select_user'],
						['title' => '选择管理员', 'url' => 'Admin/win_select_user'],
					]],
					['title' => '地区功能', 'url' => 'Area/get_area_list', 'filter_list' => [
						['title' => '选择城市', 'url' => 'Area/get_area_list'],
					]],
					['title' => '标签功能', 'url' => 'Tags/autocomplete', 'filter_list' => [
						['title' => '自动完成标签', 'url' => 'Tags/autocomplete'],
						['title' => '添加标签', 'url' => 'Tags/add_content'],
					]],
				]],
			]
		],
		[
			'title'     => '首页',
			'url'       => 'Index/system_info',
			'icon'      => 'icon-index',
			'is_select' => 0,
			'list'      => [
				['title' => '系统信息', 'url' => 'Index/system_info', 'addurl' => '', 'list' => [
					['title' => '系统信息', 'url' => 'Index/system_info', 'is_select' => 0, 'filter_list' => []],
				], 'is_select' => 0, 'filter_list' => []],
			],
		],
//		[
//			'title'     => '内容管理',
//			'url'       => 'Article/index?module=news&mark=tgkt',
//			'icon'      => 'icon-content',
//			'is_select' => 0,
//			'list'      => [
//				['title' => '关于我们', 'url' => 'Page/index?module=about_us', 'list' => [
//					['title' => '关于我们', 'url' => 'Page/index?module=about_us', 'filter_list' => [], 'is_select' => 0],
//					['title' => '企业文化', 'url' => 'Page/index?module=corporate_culture', 'filter_list' => [], 'is_select' => 0],
//				], 'filter_list' => []],
//				['title' => '内容管理', 'url' => 'Article/index?module=news&mark=tgkt', 'list' => [
//                    ['title' => '糖果空投', 'url' => 'Article/index?module=news&mark=tgkt', 'filter_list' => [
//                        ['title' => '添加', 'url' => 'Article/add?module=news&mark=tgkt', 'is_select' => 0],
//                        ['title' => '修改', 'url' => 'Article/edit?module=news&mark=tgkt', 'is_select' => 0],
//                        ['title' => '虚拟删除', 'url' => 'Article/del?module=news&mark=tgkt', 'is_select' => 0],
//                        ['title' => '启用禁用', 'url' => 'Article/checkbox?module=news&field_name=status&mark=tgkt', 'is_select' => 0],
//                        ['title' => '回收站', 'url' => 'Article/recycle?module=news&mark=tgkt', 'is_select' => 0],
//                        ['title' => '真实删除', 'url' => 'Article/delete?module=news&mark=tgkt', 'is_select' => 0],
//                        ['title' => '还原', 'url' => 'Article/permit?module=news&mark=tgkt', 'is_select' => 0],
//
//                    ], 'is_select' => 0],
//                    ['title' => '评级申请', 'url' => 'Article/index?module=news&mark=pjsq', 'filter_list' => [
//                        ['title' => '添加', 'url' => 'Article/add?module=news&mark=pjsq', 'is_select' => 0],
//                        ['title' => '修改', 'url' => 'Article/edit?module=news&mark=pjsq', 'is_select' => 0],
//                        ['title' => '虚拟删除', 'url' => 'Article/del?module=news&mark=pjsq', 'is_select' => 0],
//                        ['title' => '启用禁用', 'url' => 'Article/checkbox?module=news&field_name=status&mark=pjsq', 'is_select' => 0],
//                        ['title' => '回收站', 'url' => 'Article/recycle?module=news&mark=pjsq', 'is_select' => 0],
//                        ['title' => '真实删除', 'url' => 'Article/delete?module=news&mark=pjsq', 'is_select' => 0],
//                        ['title' => '还原', 'url' => 'Article/permit?module=news&mark=pjsq', 'is_select' => 0],
//
//                    ], 'is_select' => 0],
//                    ['title' => '帮助内容', 'url' => 'Article/index?module=news&mark=help_content', 'filter_list' => [
//                        ['title' => '添加', 'url' => 'Article/add?module=news&mark=help_content', 'is_select' => 0],
//                        ['title' => '修改', 'url' => 'Article/edit?module=news&mark=help_content', 'is_select' => 0],
//                        ['title' => '虚拟删除', 'url' => 'Article/del?module=news&mark=help_content', 'is_select' => 0],
//                        ['title' => '启用禁用', 'url' => 'Article/checkbox?module=news&field_name=status&mark=help_content', 'is_select' => 0],
//                        ['title' => '回收站', 'url' => 'Article/recycle?module=news&mark=help_content', 'is_select' => 0],
//                        ['title' => '真实删除', 'url' => 'Article/delete?module=news&mark=help_content', 'is_select' => 0],
//                        ['title' => '还原', 'url' => 'Article/permit?module=news&mark=help_content', 'is_select' => 0],
//
//                    ], 'is_select' => 0],
//                    ['title' => '活动规则', 'url' => 'Article/index?module=news&mark=hdgz_content', 'filter_list' => [
//                        ['title' => '添加', 'url' => 'Article/add?module=news&mark=hdgz_content', 'is_select' => 0],
//                        ['title' => '修改', 'url' => 'Article/edit?module=news&mark=hdgz_content', 'is_select' => 0],
//                        ['title' => '虚拟删除', 'url' => 'Article/del?module=news&mark=hdgz_content', 'is_select' => 0],
//                        ['title' => '启用禁用', 'url' => 'Article/checkbox?module=news&field_name=status&mark=hdgz_content', 'is_select' => 0],
//                        ['title' => '回收站', 'url' => 'Article/recycle?module=news&mark=hdgz_content', 'is_select' => 0],
//                        ['title' => '真实删除', 'url' => 'Article/delete?module=news&mark=hdgz_content', 'is_select' => 0],
//                        ['title' => '还原', 'url' => 'Article/permit?module=news&mark=hdgz_content', 'is_select' => 0],
//
//                    ], 'is_select' => 0],
//					['title' => '新闻资讯', 'url' => 'Article/index?module=news&mark=xwzx', 'filter_list' => [
//						['title' => '添加', 'url' => 'Article/add?module=news&mark=xwzx', 'is_select' => 0],
//						['title' => '修改', 'url' => 'Article/edit?module=news&mark=xwzx', 'is_select' => 0],
//						['title' => '虚拟删除', 'url' => 'Article/del?module=news&mark=xwzx', 'is_select' => 0],
//						['title' => '启用禁用', 'url' => 'Article/checkbox?module=news&field_name=status&mark=xwzx', 'is_select' => 0],
//						['title' => '回收站', 'url' => 'Article/recycle?module=news&mark=xwzx', 'is_select' => 0],
//						['title' => '真实删除', 'url' => 'Article/delete?module=news&mark=xwzx', 'is_select' => 0],
//						['title' => '还原', 'url' => 'Article/permit?module=news&mark=xwzx', 'is_select' => 0],
//					], 'is_select' => 0],
//					['title' => '栏目类别', 'url' => 'Category/index?module=article_news', 'filter_list' => [
//						['title' => '添加', 'url' => 'Category/add?module=article_news', 'is_select' => 0],
//						['title' => '修改', 'url' => 'Category/edit?module=article_news', 'is_select' => 0],
//						['title' => '删除', 'url' => 'Category/delete?module=article_news', 'is_select' => 0],
//						['title' => '启用禁用', 'url' => 'Category/checkbox?module=article_news&field_name=status', 'is_select' => 0],
//					], 'is_select' => 0],
//				], 'filter_list' => []],
//				['title' => '广告管理', 'url' => 'Banner/index?module=app_index', 'list' => [
//					['title' => '项目评级', 'url' => 'Banner/index?module=app_index', 'filter_list' => [
//						['title' => '添加', 'url' => 'Banner/add?module=app_index', 'is_select' => 0],
//						['title' => '修改', 'url' => 'Banner/edit?module=app_index', 'is_select' => 0],
//						['title' => '虚拟删除', 'url' => 'Banner/del?module=app_index', 'is_select' => 0],
//						['title' => '启用禁用', 'url' => 'Banner/checkbox?module=app_index&field_name=status', 'is_select' => 0],
//						['title' => '回收站', 'url' => 'Banner/recycle?module=app_index', 'is_select' => 0],
//						['title' => '真实删除', 'url' => 'Banner/delete?module=app_index', 'is_select' => 0],
//						['title' => '还原', 'url' => 'Banner/permit?module=app_index', 'is_select' => 0],
//					], 'is_select' => 0],
//					['title' => '邀请有礼', 'url' => 'Banner/index?module=invite_index', 'filter_list' => [
//						['title' => '添加', 'url' => 'Banner/add?module=invite_index', 'is_select' => 0],
//						['title' => '修改', 'url' => 'Banner/edit?module=invite_index', 'is_select' => 0],
//						['title' => '虚拟删除', 'url' => 'Banner/del?module=invite_index', 'is_select' => 0],
//						['title' => '启用禁用', 'url' => 'Banner/checkbox?module=invite_index&field_name=status', 'is_select' => 0],
//						['title' => '回收站', 'url' => 'Banner/recycle?module=invite_index', 'is_select' => 0],
//						['title' => '真实删除', 'url' => 'Banner/delete?module=invite_index', 'is_select' => 0],
//						['title' => '还原', 'url' => 'Banner/permit?module=invite_index', 'is_select' => 0],
//					], 'is_select' => 0],
//                    ['title' => '糖果空投', 'url' => 'Banner/index?module=drop_index', 'filter_list' => [
//                        ['title' => '添加', 'url' => 'Banner/add?module=drop_index', 'is_select' => 0],
//                        ['title' => '修改', 'url' => 'Banner/edit?module=drop_index', 'is_select' => 0],
//                        ['title' => '虚拟删除', 'url' => 'Banner/del?module=drop_index', 'is_select' => 0],
//                        ['title' => '启用禁用', 'url' => 'Banner/checkbox?module=drop_index&field_name=status', 'is_select' => 0],
//                        ['title' => '回收站', 'url' => 'Banner/recycle?module=drop_index', 'is_select' => 0],
//                        ['title' => '真实删除', 'url' => 'Banner/delete?module=drop_index', 'is_select' => 0],
//                        ['title' => '还原', 'url' => 'Banner/permit?module=drop_index', 'is_select' => 0],
//                    ], 'is_select' => 0],
//                    ['title' => '新闻资讯', 'url' => 'Banner/index?module=article_index', 'filter_list' => [
//                        ['title' => '添加', 'url' => 'Banner/add?module=article_index', 'is_select' => 0],
//                        ['title' => '修改', 'url' => 'Banner/edit?module=article_index', 'is_select' => 0],
//                        ['title' => '虚拟删除', 'url' => 'Banner/del?module=article_index', 'is_select' => 0],
//                        ['title' => '启用禁用', 'url' => 'Banner/checkbox?module=article_index&field_name=status', 'is_select' => 0],
//                        ['title' => '回收站', 'url' => 'Banner/recycle?module=article_index', 'is_select' => 0],
//                        ['title' => '真实删除', 'url' => 'Banner/delete?module=article_index', 'is_select' => 0],
//                        ['title' => '还原', 'url' => 'Banner/permit?module=article_index', 'is_select' => 0],
//                    ], 'is_select' => 0],
//				], 'filter_list' => []],
//                ['title' => '活动管理', 'url' => 'Activity', 'list' => [
//                    ['title' => '活动列表', 'url' => 'Activity/index', 'filter_list' => [
//                        ['title' => '添加', 'url' => 'Activity/add', 'is_select' => 0],
//                        ['title' => '修改', 'url' => 'Activity/edit', 'is_select' => 0],
//                        ['title' => '虚拟删除', 'url' => 'Activity/del', 'is_select' => 0],
//                        ['title' => '回收站', 'url' => 'Activity/recycle', 'is_select' => 0],
//                        ['title' => '真实删除', 'url' => 'Activity/delete', 'is_select' => 0],
//                        ['title' => '还原', 'url' => 'Activity/permit', 'is_select' => 0],
//                    ], 'is_select' => 0],
//                    ['title' => '活动记录列表', 'url' => 'ActivityRecord/index', 'filter_list' => [
//                        ['title' => '添加', 'url' => 'ActivityRecord/add', 'is_select' => 0],
//                        ['title' => '修改', 'url' => 'ActivityRecord/edit', 'is_select' => 0],
//                        ['title' => '虚拟删除', 'url' => 'ActivityRecord/del', 'is_select' => 0],
//                        ['title' => '启用禁用', 'url' => 'ActivityRecord/checkbox', 'is_select' => 0],
//                        ['title' => '回收站', 'url' => 'ActivityRecord/recycle', 'is_select' => 0],
//                        ['title' => '真实删除', 'url' => 'ActivityRecord/delete', 'is_select' => 0],
//                        ['title' => '还原', 'url' => 'ActivityRecord/permit', 'is_select' => 0],
//                    ], 'is_select' => 0],
//                ], 'filter_list' => []],
//                ['title' => '版本管理', 'url' => 'AppUpdate/index?module=app_update', 'list' => [
//                    ['title' => '软件更新', 'url' => 'AppUpdate/index?module=app_update', 'filter_list' => [
//                        ['title' => '添加', 'url' => 'AppUpdate/add?module=app_update', 'is_select' => 0],
//                        ['title' => '修改', 'url' => 'AppUpdate/edit?module=app_update', 'is_select' => 0],
//                        ['title' => '虚拟删除', 'url' => 'AppUpdate/del', 'is_select' => 0],
//                        ['title' => '启用禁用', 'url' => 'AppUpdate/checkbox?module=app_update&field_name=status', 'is_select' => 0],
//                    ], 'is_select' => 0],
//
//                ], 'filter_list' => []],
//			]
//		],
		[
			'title'     => '用户管理',
			'url'       => 'User/index',
			'icon'      => 'icon-user',
			'is_select' => 0,
			'list'      => [
				['title' => '用户管理', 'url' => 'User/index', 'list' => [
                    ['title' => '会员管理', 'url' => 'User/index', 'filter_list' => [
                        ['title' => '查看详情', 'url' => 'User/win_detail', 'is_select' => 0],
                        ['title' => '启用禁用', 'url' => 'User/set_status', 'is_select' => 0],
                        ['title' => '设置演示账号', 'url' => 'User/set_test', 'is_select' => 0],
                    ], 'is_select' => 0]
				], 'filter_list' => []],
			]
		],
        [
            'title'     => '提现申请',
            'url'       => 'Withdraw/index',
            'icon'      => 'icon-money',
            'is_select' => 0,
            'list'      => [
                ['title' => '提现申请', 'url' => 'Withdraw/index', 'list' => [
                    ['title' => '申请列表', 'url' => 'Withdraw/index', 'filter_list' => [
                    ], 'is_select' => 0]
                ], 'filter_list' => []],
            ]
        ],
        // [
        //     'title'     => '项目管理',
        //     'url'       => 'Project/index',
        //     'icon'      => 'icon-view',
        //     'is_select' => 0,
        //     'list'      => [
        //         ['title' => '项目管理', 'url' => 'Project/index', 'addurl' => '', 'list' => [
        //             ['title' => '列表管理', 'url' => 'Project/index', 'is_select' => 0, 'filter_list' => [
        //                 ['title' => '添加项目', 'url' => 'Project/add_project', 'is_select' => 0],
        //                 ['title' => '编辑项目', 'url' => 'Project/edit_project', 'is_select' => 0],
        //                 ['title' => '删除项目', 'url' => 'Project/del', 'is_select' => 0],
        //                 ['title' => '回收站', 'url' => 'Project/recycle', 'is_select' => 0],
        //                 ['title' => '还原', 'url' => 'Project/permit', 'is_select' => 0],
        //             ],]
        //         ], 'is_select' => 0, 'filter_list' => []],
        //     ],
        // ],
        // [
        //     'title'     => '订单管理',
        //     'url'       => 'Order/index',
        //     'icon'      => 'icon-order',
        //     'is_select' => 0,
        //     'list'      => [
        //         ['title' => '订单管理', 'url' => 'Order/index', 'addurl' => '', 'list' => [
        //             ['title' => '列表管理', 'url' => 'Order/index', 'is_select' => 0, 'filter_list' => [
        //                 ['title' => '添加项目', 'url' => 'Order/confirm', 'is_select' => 0],
        //                 ['title' => '编辑订单', 'url' => 'Order/edit', 'is_select' => 0],
        //                 ['title' => '删除项目', 'url' => 'Order/del', 'is_select' => 0],
        //                 ['title' => '回收站', 'url' => 'Order/recycle', 'is_select' => 0],
        //                 ['title' => '退单', 'url' => 'Order/orderdel', 'is_select' => 0],
        //             ],]
        //         ], 'is_select' => 0, 'filter_list' => []],
        //     ],
        // ],
        // [
        //     'title'     => '积分管理',
        //     'url'       => 'MemberCreditExchange/index',
        //     'icon'      => 'icon-money',
        //     'is_select' => 0,
        //     'list'      => [
        //         ['title' => '积分管理', 'url' => 'MemberCreditExchange/index', 'addurl' => '', 'list' => [
        //             ['title' => '积分兑换列表', 'url' => 'MemberCreditExchange/index', 'is_select' => 0, 'filter_list' => [
        //                 ['title' => '编辑', 'url' => 'MemberCreditExchange/edit', 'is_select' => 0],
        //                 ['title' => '删除', 'url' => 'MemberCreditExchange/del', 'is_select' => 0],
        //                 ['title' => '回收站', 'url' => 'MemberCreditExchange/recycle', 'is_select' => 0],
        //             ],]
        //         ], 'is_select' => 0, 'filter_list' => []],
        //     ],
        // ],
//		[
//			'title'     => '示例模板',
//			'url'       => 'Sample/page_index',
//			'icon'      => 'icon-sample',
//			'is_select' => 0,
//			'list'      => [
//				['title' => '页面示例', 'url' => 'Sample/page_index', 'addurl' => '', 'list' => [
//					['title' => '列表页面', 'url' => 'Sample/page_index', 'is_select' => 0, 'filter_list' => []],
//					['title' => '添加修改', 'url' => 'Sample/page_edit', 'is_select' => 0, 'filter_list' => []],
//					['title' => '详情页面', 'url' => 'Sample/page_detail', 'is_select' => 0, 'filter_list' => []],
//				], 'is_select' => 0, 'filter_list' => []],
//				['title' => '布局相关', 'url' => 'Sample/layout_button', 'addurl' => '', 'list' => [
//					['title' => '按钮', 'url' => 'Sample/layout_button', 'is_select' => 0, 'filter_list' => []],
//					['title' => '图标', 'url' => 'Sample/layout_icon', 'is_select' => 0, 'filter_list' => []],
//					['title' => '表单', 'url' => 'Sample/layout_form', 'is_select' => 0, 'filter_list' => []],
//					['title' => '网格', 'url' => 'Sample/layout_grid', 'is_select' => 0, 'filter_list' => []],
//				], 'is_select' => 0, 'filter_list' => []],
//				['title' => '插件', 'url' => 'Sample/plugin_upload', 'addurl' => '', 'list' => [
//					['title' => '上传插件', 'url' => 'Sample/plugin_upload', 'is_select' => 0, 'filter_list' => []],
//					['title' => '类别插件', 'url' => 'Sample/plugin_category', 'is_select' => 0, 'filter_list' => []],
//				], 'is_select' => 0, 'filter_list' => []],
//			],
//		],
		[
			'title'     => '系统设置',
			'url'       => 'Admin/detail',
			'icon'      => 'icon-system',
			'is_select' => 0,
			'list'      => [
				['title' => '个人设置', 'url' => 'Admin/detail', 'list' => [
					['title' => '个人信息', 'url' => 'Admin/detail', 'filter_list' => [], 'is_select' => 0],
					['title' => '密码修改', 'url' => 'Admin/change_password', 'filter_list' => [], 'is_select' => 0],
				], 'filter_list' => []],
				['title' => '管理员管理', 'url' => 'Admin/index', 'list' => [
					['title' => '管理员列表', 'url' => 'Admin/index', 'filter_list' => [
						['title' => '添加管理员', 'url' => 'Admin/add', 'is_select' => 0, 'filter_list' => []],
						['title' => '修改管理员', 'url' => 'Admin/edit', 'is_select' => 0, 'filter_list' => []],
					], 'is_select' => 0],
					['title' => '管理员角色', 'url' => 'AdminRole/index', 'filter_list' => [
						['title' => '添加角色', 'url' => 'AdminRole/add', 'is_select' => 0, 'filter_list' => []],
						['title' => '修改角色', 'url' => 'AdminRole/edit', 'is_select' => 0, 'filter_list' => []],
					], 'is_select' => 0],
					['title' => '操作日志', 'url' => 'Admin/operation_log', 'filter_list' => [
						
					], 'is_select' => 0],
				], 'filter_list' => []],
				['title' => '系统配置', 'url' => 'Config/setting?type=system', 'list' => [
					['title' => '站点配置', 'url' => 'Config/setting?type=system', 'filter_list' => [
						
					], 'is_select' => 0],
                    ['title' => '积分配置', 'url' => 'Config/setting?type=credit', 'filter_list' => [

                    ], 'is_select' => 0],
                    ['title' => '分享配置', 'url' => 'Config/setting?type=invite', 'filter_list' => [

                    ], 'is_select' => 0],
                    ['title' => '客服配置', 'url' => 'Config/setting?type=wxcustom', 'filter_list' => [

                    ], 'is_select' => 0],
                    ['title' => '推送配置', 'url' => 'Config/setting?type=push', 'filter_list' => [

                    ], 'is_select' => 0],
                    ['title' => '后端配置', 'url' => 'Config/setting?type=admin', 'filter_list' => [

                    ], 'is_select' => 0],
                    ['title' => '电报群、微信配置', 'url' => 'Config/setting?type=telegram', 'filter_list' => [

                    ], 'is_select' => 0]
//					['title' => '地区配置', 'url' => 'Area/index', 'filter_list' => [
//						['title' => '添加地区', 'url' => 'Area/add', 'is_select' => 0, 'filter_list' => []],
//						['title' => '修改地区', 'url' => 'Area/edit', 'is_select' => 0, 'filter_list' => []],
//						['title' => '删除地区', 'url' => 'Area/delete', 'is_select' => 0, 'filter_list' => []],
//						['title' => '标记开通', 'url' => 'Area/checkbox?field_name=is_open', 'is_select' => 0, 'filter_list' => []],
//						['title' => '标记热门', 'url' => 'Area/checkbox?field_name=is_hot', 'is_select' => 0, 'filter_list' => []],
//					], 'is_select' => 0],
				], 'filter_list' => []],
				['title' => '短信管理', 'url' => 'Config/setting?type=sms', 'list' => [
					['title' => '短信配置', 'url' => 'Config/setting?type=sms', 'filter_list' => [], 'is_select' => 0],
					['title' => '短信过滤', 'url' => 'Config/setting?type=filter_sms', 'filter_list' => [], 'is_select' => 0],
					['title' => '短信日志', 'url' => 'Sms/logs', 'filter_list' => [], 'is_select' => 0],
				], 'filter_list' => []],
//				['title' => '微信管理', 'url' => 'Config/setting?type=weixin', 'addurl' => '', 'list' => [
//					['title' => '微信配置', 'url' => 'Config/setting?type=weixin'],
//					['title' => '菜单配置', 'url' => 'WeixinMenu/index', 'filter_list' => [
//						['title' => '添加', 'url' => 'WeixinMenu/add'],
//						['title' => '修改', 'url' => 'WeixinMenu/edit'],
//						['title' => '虚拟删除', 'url' => 'WeixinMenu/del'],
//						['title' => '真实删除', 'url' => 'WeixinMenu/delete'],
//						['title' => '还原', 'url' => 'WeixinMenu/permit'],
//						['title' => '同步至微信', 'url' => 'WeixinMenu/update_menu'],
//						['title' => '启用禁用', 'url' => 'WeixinMenu/checkbox&field_name=status'],
//						['title' => '回收站', 'url' => 'WeixinMenu/recycle'],
//					]],
//					['title' => '用户清单', 'url' => 'WeixinMenu/user_list'],
//				]],
				['title' => '应用管理', 'url' => 'AppSet/index', 'list' => [
//					['title' => '接口配置', 'url' => 'Config/setting?type=api', 'filter_list' => [], 'is_select' => 0],
					['title' => '应用授权管理', 'url' => 'AppSet/index', 'filter_list' => [
						['title' => '添加', 'url' => 'AppSet/add'],
						['title' => '修改', 'url' => 'AppSet/edit'],
						['title' => '启用禁用', 'url' => 'AppSet/checkbox&field_name=status'],
					], 'is_select' => 0],
					['title' => '接口日志', 'url' => 'AppSet/logs', 'filter_list' => [], 'is_select' => 0],
				], 'filter_list' => []],
//				['title' => '文件管理', 'url' => 'File/index', 'list' => [
//					['title' => '文件管理', 'url' => 'File/index', 'filter_list' => [
//						['title' => '删除', 'url' => 'File/delete'],
//						['title' => '上传至OSS', 'url' => 'File/upload_oss'],
//						['title' => '从OSS下载', 'url' => 'File/down_oss_file'],
//					], 'is_select' => 0],
//					['title' => 'OSS配置', 'url' => 'Config/setting?type=aliyun_oss', 'filter_list' => [], 'is_select' => 0],
//				], 'filter_list' => []],
			]
		],
	]
];
