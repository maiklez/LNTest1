<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Orchestra\Parser\Xml\Facade as XmlParser;
class Scrapp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ln:scrapp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $xml = XmlParser::load('https://rss.sciencedirect.com/publication/science/15662535');
    	
        //Year,Source title,Volume,DOI,Authors,Authors with affiliations,Title,Abstract,Keywords
        $data = $xml->parse([
        		'items' => ['uses' => 'channel.item[title,description,link]'],        		
        ]);
        
        dump($data['items'][0]['link']);
        $html = new \Htmldom($data['items'][0]['link']);
        
        
        dump($html->find('div.DoiLink', 0));
        
        //Keywords
        dump(str_replace('Keywords',"", $html->find('div.Keywords', 0)->plaintext));
        
//         foreach ($data['items'] as $item){
//         	$html = new \Htmldom($item['link']);
        	 
        	
//         	dump($item['link']);
//         }
        
        #dump($data['items']);
    }
}
