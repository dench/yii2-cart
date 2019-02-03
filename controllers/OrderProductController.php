<?php

namespace dench\cart\controllers;

use dench\cart\models\Order;
use dench\products\models\Product;
use dench\products\models\Variant;
use Yii;
use dench\cart\models\OrderProduct;
use dench\cart\models\OrderProductSearch;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderProductController implements the CRUD actions for OrderProduct model.
 */
class OrderProductController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all OrderProduct models.
     * @return mixed
     */
    public function actionIndex($order_id)
    {
        $order = $this->findOrder($order_id);

        Order::read($order_id);

        $searchModel = new OrderProductSearch(['order_id' => $order_id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ($order->load(Yii::$app->request->post()) && $order->save()) {
            Yii::$app->session->setFlash('success', Yii::t('page', 'Information has been saved successfully.'));
            return $this->redirect(['/admin/cart/order/index']);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'order' => $order,
        ]);
    }

    /**
     * Creates a new OrderProduct model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($order_id)
    {
        $model = new OrderProduct();

        $model->order_id = $order_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'order_id' => $model->order_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderProduct model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $order_id = $model->order_id;
            if ($model->save()) {
                return $this->redirect(['index', 'order_id' => $order_id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing OrderProduct model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $order_id = $model->order_id;

        $model->delete();

        return $this->redirect(['index', 'order_id' => $order_id]);
    }

    public function actionProductList()
    {
        $data = [];

        $products = Product::find()->where(['enabled' => true])->all();
        foreach ($products as $product) {
            $variants = Variant::find()->where(['enabled' => true])->andWhere(['product_id' => $product->id])->all();
            foreach ($variants as $variant) {
                $data[] = [
                    'id' => $variant->id,
                    'value' => $product->name . ", " . $variant->name,
                    'price' => $variant->price,
                ];
            }
        }

        return Json::encode($data);
    }

    /**
     * Finds the OrderProduct model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderProduct the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderProduct::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    protected function findOrder($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
