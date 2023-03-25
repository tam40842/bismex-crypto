<?php
return [
	[
		'name' => 'dashboard_access',
		'priority' => 1,
		'title' => 'Bảng điều khiển',
		'url' => '/admin/dashboard',
		'icon' => 'fa fa-dashboard fa-fw',
		// 'sub' => [
		// 	['priority' => 5, 'title' => 'Home', 'url' => '/admin/dashboard']
		// ]
	],
	[
		'name' => 'media_access',
		'priority' => 2,
		'title' => 'Thư viện hình ảnh',
		'url' => '/admin/media',
		'icon' => 'fa fa-image fa-fw',
	],
	[
		'name' => 'currencies_access',
		'priority' => 10,
		'title' => 'Currencies',
		'url' => '/admin/currencies',
		'icon' => 'fa fa-btc fa-fw',
	],
	[
		'name' => 'users_access',
		'priority' => 20,
		'title' => 'Quản lý tài khoản',
		'url' => '/admin/users',
		'icon' => 'fa fa-user fa-fw',
		'sub' => [
			['priority' => 1, 'name' => 'list_users_access', 'title' => 'Tất cả tài khoản', 'url' => '/admin/users'],
			['priority' => 5, 'name' => 'verifing_users_access', 'title' => 'Yêu cầu KYC', 'url' => '/admin/users/verifing'],
			['priority' => 7, 'name' => 'richlist_users_access', 'title' => 'Top đại gia', 'url' => '/admin/users/richlist'],
			['priority' => 8, 'name' => 'prolist_users_access', 'title' => 'Top cao thủ', 'url' => '/admin/users/prolist'],
			// ['priority' => 7, 'name' => 'levels_access', 'title' => 'Level Commission', 'url' => '/admin/users/levels'],
		]
	],
	// [
	// 	'name' => 'robots_access',
	// 	'priority' => 21,
	// 	'title' => 'Quản lý Robots',
	// 	'url' => '/admin/robots',
	// 	'icon' => 'fa fa-android fa-fw',
	// ],
	[
		'name' => 'finance_access',
		'priority' => 27,
		'title' => 'Quản lý tài chính',
		'url' => '/admin/finance',
		'icon' => 'fa fa-usd fa-fw',
		'sub' => [
			['priority' => 5, 'name' => 'finance_deposit_access', 'title' => 'Các lệnh nạp tiền', 'url' => '/admin/finance/deposit'],
			['priority' => 6, 'name' => 'finance_withdraw_access', 'title' => 'Các lệnh rút tiền', 'url' => '/admin/finance/withdraw'],
			['priority' => 7, 'name' => 'finance_transfers_access', 'title' => 'Các lệnh chuyển tiền', 'url' => '/admin/finance/transfers'],
			// ['priority' => 8, 'name' => 'finance_exchange_access', 'title' => 'Các lệnh exchange', 'url' => '/admin/finance/exchange'],
			['priority' => 9, 'name' => 'finance_commissions_access', 'title' => 'Commission histories', 'url' => '/admin/finance/commissions'],
			// ['priority' => 10, 'name' => 'finance_calculator_commissions_access', 'title' => 'Calculator Commission', 'url' => '/admin/finance/calculator-commissions'],
		]
	],
	[
		'name' => 'orders_access',
		'priority' => 101,
		'title' => 'Lịch sử trade',
		'url' => '/admin/orders',
		'icon' => 'fa fa-refresh',
	],
	// [
	// 	'name' => 'policy_access',
	// 	'priority' => 27,
	// 	'title' => 'Chính sách',
	// 	'url' => '/admin/policy',
	// 	'icon' => 'fa fa-bookmark',
	// 	'sub' => [
	// 		['priority' => 5, 'name' => 'policy_commissionlevel_access', 'title' => 'Level Commission', 'url' => '/admin/policy/commissionlevel'],
	// 		['priority' => 6, 'name' => 'policy_commissionsale_access', 'title' => 'Sale Commission', 'url' => '/admin/policy/commissionsale'],
	// 		['priority' => 8, 'name' => 'policy_commissionbonus_access', 'title' => 'Bonus Commission', 'url' => '/admin/policy/commissionbonus'],
	// 	]
	// ],
	[
		'name' => 'ticket_access',
		'priority' => 100,
		'title' => 'Ticket',
		'url' => '/admin/ticket',
		'icon' => 'fa fa-ticket',
	],
	[
		'name' => 'market_access',
		'priority' => 100,
		'title' => 'Market',
		'url' => '/admin/market',
		'icon' => 'fa fa-ticket',
	],
	// [
	// 	'name' => 'comission_histories_access',
	// 	'priority' => 101,
	// 	'title' => 'Lịch sử commission',
	// 	'url' => '/admin/comission_histories',
	// 	'icon' => 'fa fa-bookmark'
	// ],
	[
		'name' => 'lastround_access',
		'priority' => 101,
		'title' => 'Lịch sử chỉnh tay',
		'url' => '/admin/lastround',
		'icon' => 'fa fa-refresh',
	],
	[
		'name' => 'auto_trade',
		'priority' => 101,
		'title' => 'AutoTrade Manage',
		'url' => '/admin/autotrade',
		'icon' => 'fa fa-android',
	],
	[
		'name' => 'tracking_balance_access',
		'priority' => 101,
		'title' => 'Tracking Balance',
		'url' => '/admin/tracking_balance',
		'icon' => 'fa fa-university',
	],
	[
		'name' => 'hand_access',
		'priority' => 101,
		'title' => 'Chỉnh tay',
		'url' => '/admin/hand',
		'icon' => 'fa fa-hand-paper-o',
	],
	[
		'name' => 'permissions_access',
		'priority' => 69,
		'title' => 'Phân quyền',
		'url' => '/admin/permissions',
		'icon' => 'fa fa-user fa-fw',
		'sub' => [
			['priority' => 1, 'name' => 'permissions_role_access', 'title' => 'Thêm quyền quản lý', 'url' => '/admin/permissions/role'],
			['priority' => 2, 'name' => 'permissions_user_access', 'title' => 'Phân quyền User', 'url' => '/admin/permissions/user'],
		]
	],
	[
		'name' => 'bulkmail_access',
		'priority' => 70,
		'title' => 'Send Email',
		'url' => '/admin/bulkmail',
		'icon' => 'fa fa-envelope-open-o fa-fw',
	],
	// [
	// 	'name' => 'copytrading_access',
	// 	'priority' => 70,
	// 	'title' => 'Copy Trading',
	// 	'url' => '/admin/copytrading',
	// 	'icon' => 'fa fa-files-o fa-fw',
	// ],
	[
		'name' => 'settings_access',
		'priority' => 102,
		'title' => 'Settings',
		'url' => '/admin/settings',
		'icon' => 'fa fa-cogs fa-fw',
		'sub' => [
			['priority' => 5, 'name' => 'settings_tradefee_access', 'title' => 'Trade fee', 'url' => '/admin/settings/tradefee'],
			['priority' => 6, 'name' => 'settings_general_access', 'title' => 'General', 'url' => '/admin/settings'],
			['priority' => 7, 'name' => 'settings_notice_access', 'title' => 'Notice - Maintenance', 'url' => '/admin/settings/notice'],
			['priority' => 10, 'name' => 'settings_notice_deposit_access', 'title' => 'Notice - Deposit', 'url' => '/admin/settings/notice/deposit'],
			// ['priority' => 8, 'name' => 'settings_setupPass_access', 'title' => 'Setup password users', 'url' => '/admin/settings/setup-pass'],
			// ['priority' => 7, 'name' => 'settings_seo_access', 'title' => 'SEO OnPage', 'url' => '/admin/settings/seo'],
			// ['priority' => 8, 'name' => 'settings_menu_access', 'title' => 'Menu', 'url' => '/admin/settings/menu']
		]
	],
];