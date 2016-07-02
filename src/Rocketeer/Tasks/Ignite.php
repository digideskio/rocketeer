<?php

/*
 * This file is part of Rocketeer
 *
 * (c) Maxime Fabre <ehtnam6@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rocketeer\Tasks;

/**
 * A task to ignite Rocketeer.
 *
 * @author Maxime Fabre <ehtnam6@gmail.com>
 */
class Ignite extends AbstractTask
{
    /**
     * A description of what the task does.
     *
     * @var string
     */
    protected $description = "Creates Rocketeer's configuration";

    /**
     * Execute ignite.
     */
    public function execute()
    {
        // Export configuration
        $path = $this->createOutsideConfiguration();

        // Replace placeholders
        $parameters = $this->getConfigurationInformations();
        $this->app->get('igniter')->updateConfiguration($path, $parameters);

        // Display info
        $folder = basename(dirname($path)).'/'.basename($path);
        $message = '<comment>The Rocketeer configuration was created at</comment> <info>'.$folder.'</info>';

        return $this->explainer->success($message);
    }

    /**
     * Get the configuration stub to use.
     *
     * @return string
     */
    protected function createOutsideConfiguration()
    {
        return $this->app->get('igniter')->exportConfiguration();
    }

    /**
     * Get the core informations to inject in the configuration created.
     *
     * @return array
     */
    protected function getConfigurationInformations()
    {
        // Replace credentials
        $repository = $this->credentials->getCurrentRepository();
        $name = basename($this->paths->getBasePath());

        $credentials = $this->credentials->getConnectionServer();
        unset($credentials['config']);

        return array_merge(
            $credentials,
            [
                'connection' => preg_replace('/#[0-9]+/', null, $this->connections->getCurrentConnectionKey()),
                'scm_repository' => $repository->endpoint,
                'scm_username' => $repository->username,
                'scm_password' => $repository->password,
                'application_name' => $this->command->ask('What is your application\'s name ? ('.$name.')', $name),
            ]
        );
    }
}
