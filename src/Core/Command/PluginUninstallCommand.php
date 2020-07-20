<?php 
namespace App\Core\Command;

use App\Core\Entity\Plugin;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Core\Repository\PluginRepository;

class PluginUninstallCommand extends Command
{
    const ARG_CODE = 'code';

    /**
     * @var string 
     */
    protected static $defaultName = 'core:plugin:uninstall';

    /**
     * @var PluginRepository
     */
    protected $pluginR;

    /**
     * @param PluginRepository $pluginRepository
     * @param string $name
     */
    public function __construct(
        PluginRepository $pluginRepository,
        string $name = null
    ) {
        $this->pluginR = $pluginRepository; 
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     * @return void 
     */
    protected function configure()
    {
        $this->setDescription('Uninstall a plugin');
        $this->addArgument(self::ARG_CODE, InputArgument::REQUIRED, 'The code of plugin');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $code = $input->getArgument(self::ARG_CODE);
        $plugin = $this->pluginR->findOneBy(['code' => $code]);
        if (!$plugin) {
            $output->writeln('The '.$code.' does not exists');
            return Command::FAILURE;
        }
        
        $this->pluginR->delete($plugin);

        return Command::SUCCESS;
    }
}
