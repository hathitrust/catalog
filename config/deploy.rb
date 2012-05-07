set :stages, %w(production staging)
set :default_stage, "staging"
require 'capistrano/ext/multistage'

set :keep_releases, 3
set :use_sudo, false
set :application, "catalog"

ssh_options[:forward_agent] = true

set :scm,         :git
set :repository,  "/htapps/dueberb.catalog/ht"


set :copy_exclude, [".git", ".DS_Store", ".gitignore", ".gitmodules", "interface/compile"]

# Call the current dir "web"
set :current_dir, "web"


# Do we need to mess with the schema?

set :branch do
  tags = `git for-each-ref refs/tags --sort=authordate --format='%(refname:short)'`.split("\n").map {|a| a.split('/').last}
  displaytags = Hash[*(`git tag -n`.split("\n").map{|a| a.split(/\s+/, 2)}.flatten)]
  default_tag = tags.last
  puts "\n\nTags:\n  " + tags.map {|a| '%-10s %s' % [a, displaytags[a]] }.join("\n  ");
  
  tag = Capistrano::CLI.ui.ask "\n Tag to deploy (make sure to push the tag first): [#{default_tag}] "
  tag = default_tag if tag.empty?
  tag
end

namespace :vf do
  task :ddd do
    puts "Hello there"
    puts "Will deploy to #{release_name}"
  end

  task :mkcompile do
    compileDir = "#{release_path}/interface/compile"
    run "mkdir -p #{compileDir}"
    run "chmod 777 #{compileDir}"
  end

  task :mkreleases do
    run "mkdir -p #{deploy_to}/releases"
  end


  task :mkDBTables do
    migrate = nil
    until ['Y', 'N'].include? migrate
      migrate =  Capistrano::CLI.ui.ask "Do you need to run a db migration as Bill (Y/N) [N] "
      migrate = 'N' if migrate.empty?
    end

    if migrate == 'Y'
      schemafile = "#{deploy_to}/#{release_name}/mysql/schema.mysql"
      password = Capistrano::CLI.ui.ask "Password: "
      run "mysql -u dueberb -h mysql-sdr -p #{password} vufind < #{schemafile}"
    end
  end
  
  task :generateFacetLists do
    run "chmod +x #{release_path}/derived_data/getall.sh"
    run "chmod +x #{release_path}/derived_data/getallOrphans.sh"
    run "#{release_path}/derived_data/getall.sh  #{release_path}/derived_data/"
    run "#{release_path}/derived_data/getallOrphans.sh  #{release_path}/derived_data/"
  end
  
end

before "deploy:update", "vf:mkreleases"
# after "deploy:create_symlink", "vf:mkDBTables"
before "deploy:create_symlink", "vf:mkcompile"
before "deploy:create_symlink", "vf:generateFacetLists"

# Undefine stuff we don't use

namespace :deploy do
  task :start do end
  task :migrate do  end
  task :stop do  end
  task :restart do  end
  task :finalize_update, :except => { :no_release => true } do
    run "chmod -R g+w #{latest_release}" if fetch(:group_writable, true)
  end
end
