<?php
use App\Http\Controllers\Vuta\Vuta;

Route::group(['prefix' => '/admin', 'middleware' => ['CheckLogin'], 'namespace' => 'Admin'], function() {
    Route::post('/login-admin', 'AdminController@postLoginAdmin')->name('login_admin');
    Route::get('/login-2fa', 'AdminController@getLogin2FaAdmin')->name('get_login_2fa');
    Route::post('/login-2fa', 'AdminController@postLogin2FaAdmin')->name('post_login_2fa');
    Route::post('/logout', 'AdminController@Logout')->name('logout');
    Route::get('/', function () {
        //redirect url permission user setup
        if(Auth::check()) {
            return Vuta::Permissions(Auth::id());
        }
        return redirect('/');
    });

    Route::middleware(['roles:admin'])->group(function() {
        Route::get('/dashboard', 'AdminController@index')->name('admin');
        Route::group(['prefix' => 'media'], function(){
            Route::get('/', 'MediaController@getMedia');
            Route::post('/', 'MediaController@postMedia');
            Route::post('lazyload', 'MediaController@getMediaLazy');
            Route::post('media-filter', 'MediaController@getMediaFilter');
            Route::post('get-media', 'MediaController@getMediaAlone');
            Route::post('save-media', 'MediaController@postSaveMedia');
            Route::post('delete-media', 'MediaController@postDeleteMedia');
            Route::post('delete-multi-media', 'MediaController@postDeleteMultiMedia');
        });
        
        Route::group(['prefix' => '/currencies'], function() {
            Route::get('/', 'CurrencyController@index')->name('admin.currencies');
            Route::group(['prefix' => '/add'], function() {
                Route::get('/', 'CurrencyController@getAdd')->name('admin.currencies.add');
                Route::post('/', 'CurrencyController@postAdd');
            });
            Route::group(['prefix' => '/edit'], function() {
                Route::get('/{id}', 'CurrencyController@getEdit')->name('admin.currencies.edit');
                Route::post('/{id}', 'CurrencyController@postEdit');
            });
            Route::post('/search', 'CurrencyController@postSearch')->name('admin.currencies.search');
        });
        
        Route::group(['prefix' => '/payments'], function() {
            Route::get('/', 'PaymentController@index')->name('admin.payments');
            Route::group(['prefix' => '/edit'], function() {
                Route::get('/{id}', 'PaymentController@getEdit')->name('admin.payments.edit');
                Route::post('/{id}', 'PaymentController@postEdit');
            });
            
        });
    
        Route::group(['prefix' => '/users'], function() {
            Route::get('/', 'UserController@index')->name('admin.users');
            Route::get('/prolist', 'UserController@prolist')->name('admin.users.prolist');
            Route::get('export', 'UserController@export')->name('admin.users.export');
            Route::post('/prolist/search', 'UserController@prolistPostSearch')->name('admin.users.prolist.search');
            Route::get('/prolist/filters', 'UserController@prolistGetfilters')->name('admin.users.prolist.filters');
            Route::get('/richlist', 'UserController@richlist')->name('admin.users.richlist');
            Route::group(['prefix' => 'add'], function() {
                Route::get('/', 'UserController@getAdd')->name('admin.users.add');
                Route::post('/', 'UserController@postAdd');
            });
            Route::group(['prefix' => 'edit'], function() {
                Route::get('/{id}', 'UserController@getEdit')->name('admin.users.edit');
                Route::post('/{id}', 'UserController@postEdit');
                Route::get('/tree/{id}', 'UserController@getUpLine')->name('admin.users.edit.tree_up_line');
            });
            
            Route::get('/banned/{id}', 'UserController@getBanned')->name('admin.users.banned');
            Route::post('/search', 'UserController@postSearch')->name('admin.users.search');
            Route::get('filters', 'UserController@getFilters')->name('admin.users.filters');

            Route::group(['prefix' => 'verifing'], function() {
                Route::get('/', 'UserController@getVerify')->name('admin.users.verifing');
                Route::get('/edit/{id}', 'UserController@getVerifyEdit')->name('admin.users.verifing.edit');
                Route::post('/edit/{id}', 'UserController@postVerifyEdit');
                Route::get('/delete/{id}', 'UserController@getVerifyDelete')->name('admin.users.verifing.delete');
                Route::post('/search', 'UserController@postVerifySearch')->name('admin.users.verifing.search');
            });
            Route::group(['prefix' => 'levels'], function() {
                Route::get('/', 'LevelController@index')->name('admin.levels');
                Route::get('/add', 'LevelController@getAdd')->name('admin.levels.add');
                Route::post('/add', 'LevelController@postAdd');
                Route::get('/edit/{id}', 'LevelController@getEdit')->name('admin.levels.edit');
                Route::post('/edit/{id}', 'LevelController@postEdit');
                Route::get('/delete/{id}', 'LevelController@getDelete')->name('admin.levels.delete');
                Route::post('/search', 'LevelController@postSearch')->name('admin.levels.search');
            });
            Route::group(['prefix' => 'balance'], function() {
                Route::get('/', 'UserController@getBalance')->name('admin.users.balance');
                Route::post('/search', 'UserController@postBalanceSearch')->name('admin.users.balance.search');
            });
        });
        
        Route::group(['prefix' => 'robots'], function() {
            Route::get('/', 'RobotController@index')->name('admin.robots');
            Route::get('/add', 'RobotController@getAdd')->name('admin.robots.add');
            Route::post('/add', 'RobotController@postAdd');
            Route::get('/edit/{id}', 'RobotController@getEdit')->name('admin.robots.edit');
            Route::post('/edit/{id}', 'RobotController@postEdit');
            Route::get('/delete/{id}', 'RobotController@getDelete')->name('admin.robots.delete');
            Route::post('/search', 'RobotController@postSearch')->name('admin.robots.search');
            Route::get('/histories/{code}', 'RobotController@InfoCode')->name('admin.robots.histories');
        });
        
        Route::group(['prefix' => '/offers'], function() {
            Route::get('/', 'OfferController@index')->name('admin.offers');
            Route::group(['prefix' => 'edit'], function() {
                Route::get('/{offer_id}', 'OfferController@getEdit')->name('admin.offers.edit');
                Route::post('/{offer_id}', 'OfferController@postEdit');
            });
            
            Route::get('/delete/{id}', 'OfferController@getDelete')->name('admin.offers.delete');
            Route::post('/search', 'OfferController@postSearch')->name('admin.offers.search');
            Route::get('/filters', 'OfferController@getFilters')->name('admin.offers.filters');
        });

        Route::group(['prefix' => 'ticket'], function() {
            Route::get('/', 'SupportController@getList')->name('admin.ticket.list');
            Route::group(['prefix' => 'edit'], function() {
                Route::get('/{ticketid}', 'SupportController@getEdit')->name('admin.ticket.edit');
                Route::post('/{ticketid}', 'SupportController@postEdit');
            });
            Route::get('/search', 'SupportController@postSearch')->name('admin.ticket.search');
            Route::get('delete/{ticketid}', 'SupportController@getDelete')->name('admin.ticket.delete');
        });

        Route::group(['prefix' => 'market'], function() {
            Route::get('/', 'MarketController@getIndex')->name('admin.market');
            Route::post('/', 'MarketController@postIndex');
        });
    
        Route::group(['prefix' => '/orders'], function() {
            Route::get('/', 'OrderController@index')->name('admin.orders');
            Route::group(['prefix' => 'edit'], function() {
                Route::get('/{id}', 'OrderController@getEdit')->name('admin.orders.edit');
                Route::post('/{id}', 'OrderController@postEdit');
            });
            Route::get('/delete/{id}', 'OrderController@getDelete')->name('admin.orders.delete');
            Route::get('/search', 'OrderController@postSearch')->name('admin.orders.search');
            Route::get('/filters', 'OrderController@getFilters')->name('admin.orders.filters');
        });
    
        // Route::group(['prefix' => '/reviews'], function() {
        //     Route::get('/', 'ReviewController@index')->name('admin.reviews');
            
        //     Route::group(['prefix' => 'add'], function() {
        //         Route::get('/', 'ReviewController@getAdd')->name('admin.reviews.add');
        //         Route::post('/', 'ReviewController@postAdd');
        //     });
        //     Route::group(['prefix' => 'edit'], function() {
        //         Route::get('/{id}', 'ReviewController@getEdit')->name('admin.reviews.edit');
        //         Route::post('/{id}', 'ReviewController@postEdit');
        //     });
            
        //     Route::get('/delete/{id}', 'ReviewController@getDelete')->name('admin.reviews.delete');
        //     Route::post('/search', 'ReviewController@postSearch')->name('admin.reviews.search');
        // });
    
        // Route::group(['prefix' => '/blacklist'], function() {
        //     Route::get('/', 'BlacklistController@index')->name('admin.blacklist');
        //     Route::group(['prefix' => 'add'], function() {
        //         Route::get('/', 'BlacklistController@getAdd')->name('admin.blacklist.add');
        //         Route::post('/', 'BlacklistController@postAdd');
        //     });
        //     Route::group(['prefix' => 'edit'], function() {
        //         Route::get('/{id}', 'BlacklistController@getEdit')->name('admin.blacklist.edit');
        //         Route::post('/{id}', 'BlacklistController@postEdit');
        //     });            
        //     Route::get('/delete/{id}', 'BlacklistController@getDelete')->name('admin.blacklist.delete');
        //     Route::post('/search', 'BlacklistController@postSearch')->name('admin.blacklist.search');
        // });

        Route::group(['prefix' => '/policy'], function() {
            Route::group(['prefix' => '/commissionlevel'], function() {
                Route::get('/', 'CommissionLevelController@index')->name('admin.policy.commissionlevel');
                Route::group(['prefix' => 'add'], function() {
                    Route::get('/', 'CommissionLevelController@getAdd')->name('admin.policy.commissionlevel.add');
                    Route::post('/', 'CommissionLevelController@postAdd');
                });
                Route::group(['prefix' => 'edit'], function() {
                    Route::get('/{id}', 'CommissionLevelController@getEdit')->name('admin.policy.commissionlevel.edit');
                Route::post('/{id}', 'CommissionLevelController@postEdit');
                });
                Route::post('/search', 'CommissionLevelController@postSearch')->name('admin.policy.commissionlevel.search');
            });
            Route::group(['prefix' => '/commissionsale'], function() {
                Route::get('/', 'CommissionSaleController@index')->name('admin.policy.commissionsale');
                Route::group(['prefix' => 'add'], function() {
                    Route::get('/', 'CommissionSaleController@getAdd')->name('admin.policy.commissionsale.add');
                    Route::post('/', 'CommissionSaleController@postAdd');
                });
                Route::group(['prefix' => 'edit'], function() {
                    Route::get('/{id}', 'CommissionSaleController@getEdit')->name('admin.policy.commissionsale.edit');
                    Route::post('/{id}', 'CommissionSaleController@postEdit');
                });
                Route::post('/search', 'CommissionSaleController@postSearch')->name('admin.policy.commissionsale.search');
            });
            Route::group(['prefix' => '/commissionbonus'], function() {
                Route::get('/', 'CommissionBonusController@getIndex')->name('admin.policy.commissionbonus');
                Route::post('/', 'CommissionBonusController@postIndex')->name('admin.policy.commissionbonus');
            });
        });

        Route::group(['prefix' => '/hand'], function() {
            Route::get('/', 'HandController@index')->name('admin.hand');
            Route::get('/list_order', 'HandController@getListOrder')->name('admin.hand.orders');
            Route::get('/last_order', 'HandController@getLastOrder')->name('admin.hand.last_orders');
            Route::post('/', 'HandController@postIndex');
        });

        Route::group(['prefix' => '/lastround'], function() {
            Route::get('/', 'OrderController@lastround')->name('admin.lastround');
            Route::get('/filters', 'OrderController@getLastroundFilters')->name('admin.lastround.filters');
            Route::get('/search', 'OrderController@postLastroundSearch')->name('admin.lastround.search');
            Route::get('/{round}', 'OrderController@getByRound')->name('admin.lastround.byround');
        });

        // Route::group(['prefix' => '/articles'], function() {
        //     Route::group(['prefix' => '/pages'], function() {
        //         Route::get('/', 'PageController@index')->name('admin.pages');
        //         Route::get('/add', 'PageController@getAdd')->name('admin.pages.add');
        //         Route::post('/add', 'PageController@postAdd');
        //         Route::get('/edit/{id}', 'PageController@getEdit')->name('admin.pages.edit');
        //         Route::post('/edit/{id}', 'PageController@postEdit');
        //         Route::get('/delete/{id}', 'PageController@getDelete')->name('admin.pages.delete');
        //         Route::post('/search', 'PageController@postSearch')->name('admin.pages.search');
        //     });
        //     Route::group(['prefix' => 'categories'], function() {
        //         Route::get('/', 'CategoriesController@getIndex')->name('admin.categories');
        //         Route::get('add', 'CategoriesController@getAdd')->name('admin.categories.add');
        //         Route::post('add', 'CategoriesController@postAdd');
        //         Route::get('edit/{id}', 'CategoriesController@getEdit')->name('admin.categories.edit');
        //         Route::post('edit/{id}', 'CategoriesController@postEdit');
        //         Route::get('delete/{id}', 'CategoriesController@getDelete')->name('admin.categories.delete');
        //         Route::post('/search', 'CategoriesController@postSearch')->name('admin.categories.search');
        //     });
        //     Route::group(['prefix' => 'posts'], function() {
        //         Route::get('/', 'PostsController@getIndex')->name('admin.posts');
        //         Route::get('add', 'PostsController@getAdd')->name('admin.posts.add');
        //         Route::post('add', 'PostsController@postAdd');
        //         Route::get('edit/{id}', 'PostsController@getEdit')->name('admin.posts.edit');
        //         Route::post('edit/{id}', 'PostsController@postEdit');
        //         Route::get('delete/{id}', 'PostsController@getDelete')->name('admin.posts.delete');
        //         Route::post('/search', 'PostsController@postSearch')->name('admin.posts.search');
        //     });
        // });
        Route::group(['prefix' => 'permissions'], function() {
            Route::group(['prefix' => 'role'], function () {
                Route::get('/', 'PermissionController@index')->name('admin.permissions.role');
                Route::group(['prefix' => 'add'], function() {
                    Route::get('', 'PermissionController@getAddRole')->name('admin.permissions.role.add');
                    Route::post('', 'PermissionController@postAddRole');
                });
                Route::group(['prefix' => 'edit'], function() {
                    Route::get('/{id}', 'PermissionController@getEdit')->name('admin.permissions.role.edit');
                    Route::post('/{id}', 'PermissionController@postEdit');
                });
    
                Route::get('/delete/{id}', 'PermissionController@getDelete')->name('admin.permissions.role.delete');
    
                Route::get('/trash', 'PermissionController@getTrash')->name('admin.permissions.role.trash');
                Route::get('/restore/{id}', 'PermissionController@getRestore')->name('admin.permissions.role.restore');
                Route::get('/deleteTrash/{id}', 'PermissionController@deleteTrash')->name('admin.permissions.role.deleteTrash');
            });
            Route::group(['prefix' => 'user'], function() {
                Route::get('/', 'PermissionController@getUser')->name('admin.permissions.user');
                Route::group(['prefix' => 'add'], function() {
                    Route::get('/', 'PermissionController@getAddUser')->name('admin.permissions.user.add');
                    Route::post('/', 'PermissionController@postAddUser');
                });
                Route::group(['prefix' => 'edit'], function() {
                    Route::get('/{id}', 'PermissionController@getEditUser')->name('admin.permissions.user.edit');
                    Route::post('/{id}', 'PermissionController@postEditUser');
                });
    
                Route::get('/delete/{id}', 'PermissionController@getDeleteUser')->name('admin.permissions.user.delete');
            });
        });
    
        Route::group(['prefix' => '/settings'], function() {
            Route::get('/', 'SettingController@index')->name('admin.settings');
            Route::post('/', 'SettingController@SaveSettings');
            Route::get('/notice', 'SettingController@getNotice')->name('admin.settings.notice');
            Route::post('/notice', 'SettingController@postNotice');
            // Route::get('/seo', 'SettingController@getSeo')->name('admin.settings.seo');
            // Route::post('/seo', 'SettingController@postSeo');
            // Route::get('/menu/{menu_id?}', 'SettingController@getMenu')->name('admin.settings.menu');
            // Route::post('/menu/{menu_id?}', 'SettingController@postMenu');
            // Route::post('/menu/add/menu-item', 'SettingController@postAddMenuItem');
            Route::get('/notice/deposit', 'SettingController@getNoticeDeposit')->name('admin.settings.notice.deposit');
            Route::post('/notice/deposit', 'SettingController@postNoticeDeposit');
            Route::group(['prefix' => '/tradefee'], function() {
                Route::get('/', 'TradeFeeController@index')->name('admin.tradefee');
                Route::post('/', 'TradeFeeController@postIndex');
                Route::post('/range', 'TradeFeeController@postRange')->name('admin.tradefee.range');
            });
            Route::post('/transfer-limit', 'SettingController@postTransferLimit')->name('admin.transfer_limit');

            Route::get('/setup-pass', 'SettingController@getSetupPass');
            Route::post('/setup-pass', 'SettingController@postSetupPass');

            Route::get('/profit_reset_time', 'SettingController@profit_reset_time')->name('admin.profit_reset_time');
        });
        
        Route::group(['prefix' => 'finance'], function() {
            Route::group(['prefix' => '/deposit'], function() {
                Route::get('/', 'DepositController@index')->name('admin.deposit');
                Route::group(['prefix' => 'add'], function() {
                    Route::get('/', 'DepositController@getAdd')->name('admin.deposit.add');
                    Route::post('/', 'DepositController@postAdd');
                });
                Route::group(['prefix' => 'edit'], function() {
                    Route::get('/{deposit_id}', 'DepositController@getEdit')->name('admin.deposit.edit');
                    Route::post('/{deposit_id}', 'DepositController@postEdit');
                });
                Route::get('/delete/{id}', 'DepositController@getDelete')->name('admin.deposit.delete');
                Route::post('/search', 'DepositController@postSearch')->name('admin.deposit.search');
                Route::get('/filters', 'DepositController@getFilters')->name('admin.deposit.filters');
                Route::get('/filter/user/{userid}', 'DepositController@filterbyUser')->name('admin.deposit.filterbyUser');
            });
            
            Route::group(['prefix' => '/withdraw'], function() {                
                Route::get('/', 'WithdrawController@index')->name('admin.withdraw');
                Route::group(['prefix' => 'add'], function() {
                    Route::get('/', 'WithdrawController@getAdd')->name('admin.withdraw.add');
                    Route::post('/', 'WithdrawController@postAdd');
                });
                Route::group(['prefix' => 'edit'], function() {
                    Route::get('/{withdraw_id}', 'WithdrawController@getEdit')->name('admin.withdraw.edit');
                    Route::post('/{withdraw_id}', 'WithdrawController@postEdit');
                });
                Route::get('/delete/{id}', 'WithdrawController@getDelete')->name('admin.withdraw.delete');
                Route::post('/search', 'WithdrawController@postSearch')->name('admin.withdraw.search');
                Route::get('/filters', 'WithdrawController@getFilters')->name('admin.withdraw.filters');
                Route::get('/filter/user/{userid}', 'WithdrawController@filterbyUser')->name('admin.withdraw.filterbyUser');
                Route::get('/approved/{withdraw_id}', 'WithdrawController@getApproved')->name('admin.withdraw.approved');
                Route::get('/cancelled/{withdraw_id}', 'WithdrawController@getCancelled')->name('admin.withdraw.cancelled');
            });
    
            Route::group(['prefix' => '/transfers'], function() {
                Route::get('/', 'TransfersController@index')->name('admin.transfers');
                Route::group(['prefix' => 'add'], function() {
                    Route::get('/add', 'TransfersController@getAdd')->name('admin.transfers.add');
                    Route::post('/add', 'TransfersController@postAdd');
                });
                Route::group(['prefix' => 'edit'], function() {
                    Route::get('/{transfers_id}', 'TransfersController@getEdit')->name('admin.transfers.edit');
                    // Route::post('/{transfers_id}', 'TransfersController@postEdit');
                });
                
                Route::get('/delete/{id}', 'TransfersController@getDelete')->name('admin.transfers.delete');
                Route::get('/search', 'TransfersController@postSearch')->name('admin.transfers.search');
                Route::get('/filters', 'TransfersController@getFilters')->name('admin.transfers.filters');
                Route::get('/filter/user/{userid}', 'TransfersController@filterbyUser')->name('admin.transfers.filterbyUser');
                Route::get('/approved/{transfers_id}', 'TransfersController@getApproved')->name('admin.transfers.approved');
                Route::get('/cancelled/{transfers_id}', 'TransfersController@getCancelled')->name('admin.transfers.cancelled');
            });
    
            Route::group(['prefix' => '/exchange'], function() {
                Route::get('/', 'ExchangeController@index')->name('admin.exchange');
                Route::group(['prefix' => 'edit'], function() {
                    Route::get('/{id}', 'ExchangeController@getEdit')->name('admin.exchange.edit');
                    Route::post('/{id}', 'ExchangeController@postEdit');
                });
                
                Route::get('/delete/{id}', 'ExchangeController@getDelete')->name('admin.exchange.delete');
                Route::get('/search', 'ExchangeController@postSearch')->name('admin.exchange.search');
                Route::get('/filters', 'ExchangeController@getFilters')->name('admin.exchange.filters');
                Route::get('/filter/user/{userid}', 'ExchangeController@filterbyUser')->name('admin.exchange.filterbyUser');
                Route::get('/approved/{exchange_id}', 'ExchangeController@getApproved')->name('admin.exchange.approved');
                Route::get('/cancelled/{exchange_id}', 'ExchangeController@getCancelled')->name('admin.exchange.cancelled');
            });
    
            Route::group(['prefix' => '/commissions'], function() {
                Route::get('/', 'CommissionHistories@index')->name('admin.commissions');
                Route::get('/filters', 'CommissionHistories@getFilters')->name('admin.commissions.filters');
                Route::get('/search', 'CommissionHistories@postSearch')->name('admin.commissions.search');
            });
    
            Route::group(['prefix' => '/calculator-commissions'], function() {
                Route::get('/', 'CalculatorCommissionController@getIndex')->name('admin.commissions.calculator');
                Route::post('/search', 'CalculatorCommissionController@postSearch')->name('admin.calculator.search');
                Route::post('/bonus-user', 'CalculatorCommissionController@postBonusUser')->name('admin.calculator.BonusUser');
            });
        });

        Route::group(['prefix' => '/bulkmail'], function() {
            Route::get('/', 'BulkMail@index')->name('admin.bulkmail');
            Route::post('/', 'BulkMail@Send');
        });

        Route::group(['prefix' => '/copytrading'], function() {
            Route::get('/', 'CopytradingController@index')->name('admin.copytrading');
            Route::group(['prefix' => 'add'], function() {
                Route::get('/add', 'CopytradingController@getAdd')->name('admin.copytrading.add');
                Route::post('/add', 'CopytradingController@postAdd');
            });
            Route::group(['prefix' => 'edit'], function() {
                Route::get('/{id}', 'CopytradingController@getEdit')->name('admin.copytrading.edit');
                Route::post('/{id}', 'CopytradingController@postEdit');
            });
            
            Route::get('/delete/{id}', 'CopytradingController@getDelete')->name('admin.copytrading.delete');
            Route::get('/search', 'CopytradingController@postSearch')->name('admin.copytrading.search');
            Route::get('/filters', 'CopytradingController@getFilters')->name('admin.copytrading.filters');
            Route::get('/filter/user/{userid}', 'CopytradingController@filterbyUser')->name('admin.copytrading.filterbyUser');
            Route::get('/approved/{id}', 'CopytradingController@getApproved')->name('admin.copytrading.approved');
            Route::get('/cancelled/{id}', 'CopytradingController@getCancelled')->name('admin.copytrading.cancelled');
        });

        Route::group(['prefix' => '/tracking_balance'], function() {
            Route::get('/', 'TransactionsController@index')->name('admin.transactions');
            Route::get('/search', 'TransactionsController@postSearch')->name('admin.transactions.search');
            Route::get('/filters', 'TransactionsController@getFilters')->name('admin.transactions.filters');
        });

        Route::group(['prefix' => '/autotrade'], function() {
            Route::get('/', 'AutoTradeController@index')->name('admin.autotrade');
            Route::get('/search', 'AutoTradeController@postSearch')->name('admin.autotrade.search');
            Route::get('/filters', 'AutoTradeController@getFilters')->name('admin.autotrade.filters');
            Route::get('/getEdit/{id}', 'AutoTradeController@getEdit')->name('admin.autotrade.edit');
            Route::get('/cancel/{package_id}', 'AutoTradeController@postWithdrawCancel')->name('admin.autotrade.cancelled');
            Route::get('/approval/{package_id}', 'AutoTradeController@postWithdrawApproval')->name('admin.autotrade.approved');
        });
    });
});