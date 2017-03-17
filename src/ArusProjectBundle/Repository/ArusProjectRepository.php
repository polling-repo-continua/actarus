<?php

namespace ArusProjectBundle\Repository;

use Actarus\Utils;


/**
 * ArusProjectRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArusProjectRepository extends \Doctrine\ORM\EntityRepository
{
	public function findArray()
	{
		$tab = array();
		$r = $this->findAll();

		foreach( $r as $o ) {
			$tab[ $o->getId() ] = ucfirst( $o->getName() );
		}

		return $tab;
	}


	public function findAll()
	{
		$qb = $this->createQueryBuilder('p');
		$query = $qb->select(array('p'))
					->orderBy('p.name', 'ASC');

		$t_result = $query->getQuery()->getResult();

		return $t_result;
	}


	public function search( $data, $offset=null, $limit=null )
	{
		$data = Utils::array2object( $data, 'ArusProjectBundle\Entity\Search' );
		$t_params = array();
		$qb = $this->_em->createQueryBuilder();

		if( $offset < 0 ) {
			$offset = null;
			$count  = true;
			$query  = $qb->select( 'count(p.id)' );
		} else {
			$count  = false;
			$query  = $qb->select( array('p') );
		}
		$query = $query->from('ArusProjectBundle:ArusProject','p');

		if( $data )
		{
			if( ($id=$data->getId()) ) {
				if( !is_array($id) ) {
					$id = [ $id, '=' ];
				}
				$query->andWhere( 'p.id '.$id[1].' :id' );
				$t_params['id'] = $id[0];
			}
			if( ($name=$data->getName()) ) {
				if( !is_array($name) ) {
					$name = [ '%'.$name.'%', 'LIKE' ];
				}
				$query->andWhere('p.name '.$name[1].' :name');
				$t_params['name'] = $name[0];
			}
			if ( !is_null($data->getStatus()) ) {
				$status = $data->getStatus();
				if( !is_array($status) ) {
					$status = [ $status, '=' ];
				}
				$query->andWhere('p.status '.$status[1].' :status');
				$t_params['status'] = $status[0];
			}
			if ($data->getMinCreatedAt()) {
				$query->andWhere('p.createdAt >= :min_created_date');
				$t_params['min_created_date'] = date( 'Y-m-d 00:00:00', Utils::dateFR2Time($data->getMinCreatedAt()) );
			}
			if ($data->getMaxCreatedAt()) {
				$query->andWhere('p.createdAt <= :max_created_date');
				$t_params['max_created_date'] = date( 'Y-m-d 23:59:59', Utils::dateFR2Time($data->getMaxCreatedAt()) );
			}
		}

		$query->setParameters( $t_params );
		$query->orderBy('p.name', 'ASC');
		if( !is_null($limit) ) {
			$query->setMaxResults( $limit );
		}
		if( !is_null($offset) ) {
			$query->setFirstResult($offset);
		}

		$t_result = $query->getQuery()->getResult();

		if( $count ) {
			return (int)$t_result[0][1];
		} else {
			return $t_result;
		}
	}
}