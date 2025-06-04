<?php

namespace App\Services\Menu;

use App\Services\Menu\Items\BackToDashboardMenu;
use App\Services\Menu\Items\EgiUploadPageMenu;
use App\Services\Menu\Items\PermissionsRolesMenu;
use App\Services\Menu\Items\AssignRolesMenu;
use App\Services\Menu\Items\AssignPermissionsMenu;
use App\Services\Menu\Items\OpenCollectionMenu;
use App\Services\Menu\Items\NewCollectionMenu;
// Nuovi item per i menu contestuali
use App\Services\Menu\Items\StatisticsMenu;
use App\Services\Menu\Items\WalletMenu;
use App\Services\Menu\Items\AccountDataMenu;
use App\Services\Menu\Items\ActivityLogMenu;
use App\Services\Menu\Items\BioProfileMenu;
use App\Services\Menu\Items\BreachReportMenu;
use App\Services\Menu\Items\NotificationsMenu;
use App\Services\Menu\Items\ConsentMenu;
use App\Services\Menu\Items\DeleteAccountMenu;
use App\Services\Menu\Items\DocumentationMenu;
use App\Services\Menu\Items\EditPersonalDataMenu;
use App\Services\Menu\Items\ExportDataMenu;
use App\Services\Menu\Items\LimitProcessingMenu;
use App\Services\Menu\Items\PrivacyPolicyMenu;

/**
 * @Oracode Service: Context-aware Menu Provider
 * 🎯 Purpose: Provides appropriate menus based on application context
 * 🧱 Core Logic: Factory method for context-specific menu groups
 *
 * @package App\Services\Menu
 * @version 2.0
 */
class ContextMenus
{
    /**
     * Get menu groups for specific application context
     *
     * @param string $context The current application context
     * @return array Array of MenuGroup objects for the context
     */
    public static function getMenusForContext(string $context): array
    {
        $menus = [];

        switch ($context) {
            case 'dashboard':
                // Dashboard main menu
                $collectionsMenu = new MenuGroup(__('menu.collections'), 'folder_collection', [
                    new OpenCollectionMenu(),
                    new NewCollectionMenu(),
                ]);
                $menus[] = $collectionsMenu;

                // Statistics menu
                $statsMenu = new MenuGroup(__('menu.statistics'), 'chart-bar', [
                    new StatisticsMenu(),
                ]);
                $menus[] = $statsMenu;

                // Notifications menu
                $notificationsMenu = new MenuGroup(__('menu.notifications'), 'bell', [
                    new NotificationsMenu(),
                ]);
                $menus[] = $notificationsMenu;

                // Personal data menu
                $personalDataMenu = new MenuGroup(__('menu.personal_data'), 'user-cog', [
                    new AccountDataMenu(),
                    new BioProfileMenu(),
                ]);
                $menus[] = $personalDataMenu;

                // Wallet menu
                $walletMenu = new MenuGroup(__('menu.wallet'), 'wallet', [
                    new WalletMenu(),
                ]);
                $menus[] = $walletMenu;

                // GDPR menu
                $gdprMenu = new MenuGroup(__('menu.gdpr_privacy'), 'shield', [
                    new ConsentMenu(),
                    new ExportDataMenu(),
                    new EditPersonalDataMenu(),
                    new LimitProcessingMenu(),
                    new DeleteAccountMenu(),
                    new ActivityLogMenu(),
                    new BreachReportMenu(),
                    new PrivacyPolicyMenu(),
                    new BackToDashboardMenu(),
                ]);
                $menus[] = $gdprMenu;

                // Documentation menu
                $docsMenu = new MenuGroup(__('menu.documentation'), 'book', [
                    new DocumentationMenu(),
                ]);
                $menus[] = $docsMenu;

                // Admin tools menu (only for admin users)
                $adminMenu = new MenuGroup(__('menu.admin_tools'), 'tools', [
                    new PermissionsRolesMenu(),
                    new AssignRolesMenu(),
                    new AssignPermissionsMenu(),
                ]);
                $menus[] = $adminMenu;
                break;

            case 'collections':
                // Collections context menu
                $collectionsMenu = new MenuGroup(__('menu.collections'), 'folder_collection', [
                    new OpenCollectionMenu(),
                    new NewCollectionMenu(),
                    new BackToDashboardMenu(),
                ]);
                $menus[] = $collectionsMenu;
                break;

            case 'consents':

                $gdprPrivacyMenu = new MenuGroup(__('menu.gdpr_privacy'), 'shield', [
                    new ConsentMenu(),
                    new ExportDataMenu(),
                    new EditPersonalDataMenu(),
                    new LimitProcessingMenu(),
                    new DeleteAccountMenu(),
                    new ActivityLogMenu(),
                    new BreachReportMenu(),
                    new PrivacyPolicyMenu(),
                    new BackToDashboardMenu(),
                ]);
                $menus[] = $gdprPrivacyMenu;

                break;

            case 'statistics':

                $statisticsMenu = new MenuGroup(__('menu.statistics'), 'chart-bar', [
                    new StatisticsMenu(),
                    new BackToDashboardMenu(),
                ]);
                $menus[] = $statisticsMenu;
                break;

        }

        return $menus;
    }
}
