<?php

namespace GeekCms\Pages\Database\Seeders;

use Illuminate\Database\Seeder;
use GeekCms\Pages\Models\Page;

class PagesDatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->createHome();
        $this->createContacts();
    }

    public function createContacts()
    {
        $contacts = new Page([
            'type' => 'page',
            'lang' => 'en',
            'theme' => 'default',
            'name' => 'Contacts',
            'slug' => 'contacts',
            'content' => '<h1 style="text-align: center;">Contacts</h1><table style="width: 100%;"><tbody><tr><td style="width: 18.7539%; vertical-align: top;"><div data-empty="true"><a href="https://www.google.com.ua/maps/@50.4278722,30.5798399,13.25z" rel="noopener noreferrer" target="_blank"><img src="https://media.wired.com/photos/59269cd37034dc5f91bec0f1/master/pass/GoogleMapTA.jpg" style="width: 300px;" class="fr-fic fr-dib fr-fil"></a></div></td><td style="width: 35.8255%; text-align: left; vertical-align: top;"><h2 style="text-align: center;">Fedback</h2><ul><li>+38 (095) 940-25-60</li><li><a href="mailto:%20n.ponich@gmail.com">n.ponich@gmail.com</a></li></ul></td><td style="width: 45.3583%; text-align: left; vertical-align: top;"><h2 style="text-align: center;">Social networks</h2><div data-empty="true"><br></div><ul><li><a href="vk.com"></a><a href="vk.com">vk.com</a></li><li><a href="facebook.com"></a><a href="facebook.com">facebook.com</a></li><li><a href="youtube.com"></a><a href="youtube.com">youtube.com</a></li><li><a href="twitter.com"></a><a href="twitter.com">twitter.com</a></li></ul><div data-empty="true"><br></div></td></tr></tbody></table><p><br></p><p><br></p>',
        ]);

        $contacts->save();

        $contactsRu = new Page([
            'type' => 'trans',
            'lang' => 'ru',
            'parent_id' => $contacts->id,
            'name' => 'Контакты',
            'slug' => 'ru',
            'content' => '<h1 style="text-align: center;">Контакты</h1><table style="width: 100%;"><tbody><tr><td style="width: 18.7539%; vertical-align: top;"><div data-empty="true"><a href="https://www.google.com.ua/maps/@50.4278722,30.5798399,13.25z" rel="noopener noreferrer" target="_blank"><img src="https://media.wired.com/photos/59269cd37034dc5f91bec0f1/master/pass/GoogleMapTA.jpg" style="width: 300px;" class="fr-fic fr-dib fr-fil"></a></div></td><td style="width: 35.8255%; text-align: left; vertical-align: top;"><h2 style="text-align: center;">Связь</h2><ul><li>+38 (095) 940-25-60</li><li><a href="mailto:%20n.ponich@gmail.com">n.ponich@gmail.com</a></li></ul></td><td style="width: 45.3583%; text-align: left; vertical-align: top;"><h2 style="text-align: center;">Социальные сети</h2><div data-empty="true"><br></div><ul><li><a href="vk.com"></a><a href="vk.com">vk.com</a></li><li><a href="facebook.com"></a><a href="facebook.com">facebook.com</a></li><li><a href="youtube.com"></a><a href="youtube.com">youtube.com</a></li><li><a href="twitter.com"></a><a href="twitter.com">twitter.com</a></li></ul><div data-empty="true"><br></div></td></tr></tbody></table><p><br></p><p><br></p>',
        ]);

        $contactsRu->save();
    }

    public function createHome()
    {
        $home = new Page([
            'type' => 'page',
            'theme' => 'default',
            'name' => 'Home',
            'slug' => 'home',
            'lang' => 'en',
            'content' => '<p>Home page</p>',
        ]);

        $home->save();

        $homeRu = new Page([
            'type' => 'trans',
            'parent_id' => $home->id,
            'name' => 'Главная',
            'slug' => 'ru',
            'lang' => 'ru',
            'content' => '<p>Главная страница</p>',
        ]);

        $homeRu->save();
    }
}
