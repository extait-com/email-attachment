<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the commercial license
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category Extait
 * @package Extait_Attachment
 * @copyright Copyright (c) 2016-2018 Extait, Inc. (http://www.extait.com)
 */

namespace Extait\Attachment\Console\Command;

use Extait\Attachment\Mail\Template\TransportBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\MailException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestAttachment extends Command
{
    /**@#+
     * Test Data.
     */
    const TEST_TEMPLATE_IDENTIFIER = 'extait_test_attachment';
    const TEST_FROM_EMAIL = 'general';
    const TEST_TO_EMAIL = 'dchizhov@mail.extait.local';
    const TEST_FILE_CONTENT = 'Test file content.';
    const TEST_FILE_NAME = 'test';
    const TEST_FILE_TYPE = 'txt';
    /**@#-*/

    /**
     * @var \Extait\Attachment\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * TestAttachment constructor.
     *
     * @param \Extait\Attachment\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\App\State $state
     * @param null $name
     */
    public function __construct(TransportBuilder $transportBuilder, State $state, $name = null)
    {
        parent::__construct($name);

        $this->transportBuilder = $transportBuilder;
        $this->state = $state;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('extait:test:attachment');
        $this->setDescription('Test email attachment');
    }

    /**
     * Send test email and write a successful message.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode(Area::AREA_FRONTEND);

            $this->transportBuilder
                ->setTemplateIdentifier(self::TEST_TEMPLATE_IDENTIFIER)
                ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => 1])
                ->setTemplateVars([])
                ->addAttachment(self::TEST_FILE_CONTENT, self::TEST_FILE_NAME, self::TEST_FILE_TYPE)
                ->setFrom(self::TEST_FROM_EMAIL)
                ->addTo(self::TEST_TO_EMAIL)
                ->getTransport()
                ->sendMessage();

            $output->writeln(__('Everything is fine, email has been sent.'));
        } catch (MailException $me) {
            $output->writeln(__('MailException: %1', $me->getMessage()));
        } catch (\Exception $e) {
            $output->writeln(__('Exception: %1', $e->getMessage()));
        }
    }
}
