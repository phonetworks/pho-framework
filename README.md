<p align="center">
  <img width="375" height="150" src="https://github.com/phonetworks/commons-php/raw/master/.github/cover-smaller.png">
</p>

# Pho-Framework [![Build Status](https://travis-ci.org/phonetworks/pho-framework.svg?branch=master)](https://travis-ci.org/phonetworks/pho-framework) [![Code Climate](https://img.shields.io/codeclimate/github/phonetworks/pho-framework.svg)](https://codeclimate.com/github/phonetworks/pho-framework)

Pho-Framework is the foundational component of Pho Stack. It establishes
the object-centered actor/graph framework that all Pho components are built upon. It is stateless, which means, it doesn't provide persistence of its objects in any way, but it is designed for such extensibility via hydrator functions.


## Install

The recommended way to install pho-framework is through composer.

```composer require phonetworks/pho-framework```

## Documentation

For more infomation on the internals of pho-lib-graph, as well as a simple user guide, please refer to the [docs/](https://github.com/phonetworks/pho-framework/tree/master/docs) folder. You may also generate the APIs using phpdoc as described in [CONTRIBUTING.md](https://github.com/phonetworks/pho-framework/blob/master/CONTRIBUTING.md)

## FAQ

* **Is there a way to save the graph in a file or on disk?** 
Pho-Framework has no built-in server or mechanism for saving/storing/replacing the graph. It is built purely in memory. But you can use [pho-microkernel](https://github.com/phonetworks/pho-framework) for such persistence, and more (access control lists etc.)

## License

MIT, see [LICENSE](https://github.com/phonetworks/pho-framework/blob/master/LICENSE).
